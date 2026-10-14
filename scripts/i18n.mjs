#!/usr/bin/env node
/* eslint-disable no-console -- CLI script: console output is the interface. */
/**
 * PixVisualize — i18n helper (Node + gettext-parser).
 *
 * Commands:
 *   node scripts/i18n.mjs extract              Regenerate languages/pixvisualize.pot
 *                                              from the PHP + JS sources (msgids + refs),
 *                                              then list any .pot msgid missing from
 *                                              every existing language .po.
 *   node scripts/i18n.mjs compile [locale]     Read every languages/pixvisualize-{locale}.po
 *                                              (or only the given locale) and generate:
 *                                                - languages/pixvisualize-{locale}.mo
 *                                                - one wp.i18n JSON file per script
 *                                                  handle, split by file reference,
 *                                              with round-trip validation (.mo re-parsed,
 *                                              JSON re-parsed) and a warning for any JS
 *                                              source string left untranslated.
 *   node scripts/i18n.mjs watch                Watch languages/ and auto-compile whenever
 *                                              a .po file is added or modified.
 *
 * File → handle mapping used by `compile`:
 *   src/pixvisualize/edit.js, index.js, block.json
 *     (or build/pixvisualize/*)                 → create-block-pixvisualize-editor-script
 *   src/pixvisualize/view.js                    → create-block-pixvisualize-view
 *   src/pixvisualize/components/*, hooks/*, utils/*
 *     (bundled in BOTH editor and view builds) → both handles above
 *   pixvisualize-admin.js                       → pixvisualize-admin
 *   pixvisualize-woocommerce-metabox.js         → pixvisualize-woocommerce-metabox
 */

import {
	existsSync,
	mkdirSync,
	readFileSync,
	readdirSync,
	statSync,
	watch,
	writeFileSync,
} from 'node:fs';
import path from 'node:path';
import gettextParser from 'gettext-parser';

const { po, mo } = gettextParser;

const DOMAIN = 'pixvisualize';
const LANG_DIR = path.join( process.cwd(), 'languages' );

const HANDLE_ADMIN = 'pixvisualize-admin';
const HANDLE_EDITOR = 'create-block-pixvisualize-editor-script';
const HANDLE_VIEW = 'create-block-pixvisualize-view';
const HANDLE_WOO = 'pixvisualize-woocommerce-metabox';

const DEFAULT_PLURAL_FORMS = 'nplurals=2; plural=(n != 1);';

const PHP_PATTERN =
	/(?:esc_html__|esc_attr__|esc_html_e|esc_attr_e|__)\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'pixvisualize'\s*\)/g;
const JS_PATTERN =
	/(?:wp\.i18n\.)?__\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'pixvisualize'\s*\)/g;

/**
 * Recursively list files with one of the given extensions.
 *
 * @param {string}   dir Root directory.
 * @param {string[]} ext File extensions to keep (e.g. ['.js']).
 * @param {string[]} out Accumulator.
 * @return {string[]} Matching absolute file paths.
 */
function walk( dir, ext, out = [] ) {
	if ( ! existsSync( dir ) ) {
		return out;
	}

	for ( const entry of readdirSync( dir ) ) {
		const full = path.join( dir, entry );
		const stat = statSync( full );

		if ( stat.isDirectory() ) {
			walk( full, ext, out );
		} else if ( ext.some( ( e ) => full.endsWith( e ) ) ) {
			out.push( full );
		}
	}

	return out;
}

/**
 * Collect every translatable msgid from the PHP and JS sources.
 *
 * @param {boolean} jsOnly When true, only scan the JS sources.
 * @return {Map<string, Set<string>>} msgid → set of "path:line" references.
 */
function collectSources( jsOnly = false ) {
	const root = process.cwd();
	const msgids = new Map();

	const add = ( context, rel, line, msgid ) => {
		const key = context ? context + '\u0004' + msgid : msgid;
		if ( ! msgids.has( key ) ) {
			msgids.set( key, { context, msgid, refs: new Set() } );
		}
		msgids.get( key ).refs.add( rel + ':' + line );
	};

	let files = [
		...walk( path.join( root, 'src', 'js' ), [ '.js' ] ),
		...walk( path.join( root, 'src', 'pixvisualize' ), [ '.js', '.jsx' ] ),
		path.join(
			root,
			'assets',
			'js',
			'pixvisualize-woocommerce-metabox.js'
		),
	];

	if ( ! jsOnly ) {
		files = [
			path.join( root, 'pixvisualize.php' ),
			...walk( path.join( root, 'inc' ), [ '.php' ] ),
			...walk( path.join( root, 'templates' ), [ '.php' ] ),
			...files,
		];
	}

	for ( const file of files ) {
		if ( ! existsSync( file ) ) {
			continue;
		}

		const content = readFileSync( file, 'utf8' );
		const rel = path.relative( root, file ).split( path.sep ).join( '/' );
		const re = file.endsWith( '.js' ) ? JS_PATTERN : PHP_PATTERN;
		let match;

		while ( ( match = re.exec( content ) ) !== null ) {
			const line = content.slice( 0, match.index ).split( '\n' ).length;
			const msgid = match[ 1 ].replace( /\\(['\\])/g, '$1' );
			add( '', rel, line, msgid );
		}
	}

	// block.json metadata (title, description, keywords) is localized in the
	// editor via the block's textdomain, so those strings must be extracted too.
	const blockJsonFiles = walk( path.join( root, 'src' ), [ 'block.json' ] );

	for ( const file of blockJsonFiles ) {
		if ( ! existsSync( file ) ) {
			continue;
		}

		const content = readFileSync( file, 'utf8' );
		let data;

		try {
			data = JSON.parse( content );
		} catch {
			continue;
		}

		const rel = path.relative( root, file ).split( path.sep ).join( '/' );
		const lines = content.split( '\n' );
		const lineFor = ( needle ) => {
			if ( ! needle ) {
				return -1;
			}
			const idx = lines.findIndex( ( l ) => l.includes( needle ) );
			return idx === -1 ? -1 : idx + 1;
		};

		const strings = [
			{ value: data.title, context: 'block title' },
			{ value: data.description, context: 'block description' },
			...( data.keywords || [] ).map( ( keyword ) => ( {
				value: keyword,
				context: 'block keyword',
			} ) ),
		];

		for ( const { value, context } of strings ) {
			if ( ! value ) {
				continue;
			}
			const line = lineFor( value );
			if ( line !== -1 ) {
				add( context, rel, line, value );
			}
		}
	}

	return msgids;
}

/**
 * Build a gettext-parser PO data object from a msgid collection.
 *
 * @param {Map<string, Set<string>>} msgids msgid → references.
 * @return {Object} PO table.
 */
function buildPoTable( msgids ) {
	const translations = { '': {} };

	for ( const { context, msgid, refs } of msgids.values() ) {
		const ctx = context || '';
		if ( ! translations[ ctx ] ) {
			translations[ ctx ] = {};
		}
		translations[ ctx ][ msgid ] = {
			msgid,
			...( ctx ? { msgctxt: ctx } : {} ),
			comments: { reference: [ ...refs ].join( '\n' ) },
			msgstr: [ '' ],
		};
	}

	return {
		charset: 'UTF-8',
		headers: {
			'project-id-version': 'PixVisualize 1.0.0',
			'report-msgid-bugs-to': '',
			'pot-creation-date': utcDate(),
			'po-revision-date': 'YEAR-MO-DA HO:MI+ZONE',
			'language-team': '',
			'mime-version': '1.0',
			'content-type': 'text/plain; charset=UTF-8',
			'content-transfer-encoding': '8bit',
			'plural-forms': DEFAULT_PLURAL_FORMS,
			'x-generator': 'scripts/i18n.mjs',
		},
		translations,
	};
}

/**
 * List every language locale with a translation file in languages/.
 *
 * @return {string[]} Locales like ['fr_FR'].
 */
function listLocales() {
	if ( ! existsSync( LANG_DIR ) ) {
		return [];
	}

	return readdirSync( LANG_DIR )
		.filter( ( f ) => f.startsWith( DOMAIN + '-' ) && f.endsWith( '.po' ) )
		.map( ( f ) => f.slice( DOMAIN.length + 1, -'.po'.length ) );
}

/**
 * Extract a locale from a .po filename (e.g. "pixvisualize-de_DE.po" → "de_DE").
 *
 * @param {string} filename File name.
 * @return {string|null} Locale, or null when the name does not match.
 */
function localeFromFilename( filename ) {
	const prefix = DOMAIN + '-';
	if ( ! filename.startsWith( prefix ) || ! filename.endsWith( '.po' ) ) {
		return null;
	}
	return filename.slice( prefix.length, -'.po'.length );
}

/**
 * Derive the language code from a locale ("fr_FR" → "fr", "pt_BR" → "pt").
 *
 * @param {string} locale Locale, e.g. "fr_FR".
 * @return {string} Language code.
 */
function langCodeForLocale( locale ) {
	return locale.split( '_' )[ 0 ];
}

/**
 * Resolve the plural-forms expression from a parsed PO, falling back to English.
 *
 * @param {Object} poData Parsed gettext PO data.
 * @return {string} Plural forms expression.
 */
function pluralFormsFor( poData ) {
	const h = poData.headers || {};
	return (
		h[ 'plural-forms' ] ||
		h[ 'Plural-Forms' ] ||
		h.plural_forms ||
		DEFAULT_PLURAL_FORMS
	);
}

/**
 * Map a source file reference to its script handle(s).
 *
 * Components/hooks/utils are bundled in BOTH the editor and the view builds,
 * so their strings go into both JSON files.
 *
 * @param {string} ref "path:line" reference from the .po.
 * @return {string[]} Script handles (empty for PHP-only strings).
 */
function handlesForFile( ref ) {
	const file = ref.replace( /\\/g, '/' );

	if ( file.includes( 'src/pixvisualize/view.js' ) ) {
		return [ HANDLE_VIEW ];
	}
	if (
		file.includes( 'src/pixvisualize/components/' ) ||
		file.includes( 'src/pixvisualize/hooks/' ) ||
		file.includes( 'src/pixvisualize/utils/' )
	) {
		return [ HANDLE_EDITOR, HANDLE_VIEW ];
	}
	if (
		file.includes( 'src/pixvisualize/' ) ||
		file.includes( 'build/pixvisualize/' )
	) {
		return [ HANDLE_EDITOR ];
	}
	if ( file.includes( 'pixvisualize-admin.js' ) ) {
		return [ HANDLE_ADMIN ];
	}
	if ( file.includes( 'pixvisualize-woocommerce-metabox.js' ) ) {
		return [ HANDLE_WOO ];
	}

	return [];
}

/**
 * Build the wp.i18n JSON payload (domain "messages") for a handle.
 *
 * Values must be ARRAYS of plural forms — @wordpress/i18n (tannin) indexes the
 * entry with the plural-form index (0 for singular), so a plain string would
 * only ever return its first character.
 *
 * @param {Object} translations msgid → array of plural forms.
 * @param {string} locale       Locale, e.g. "fr_FR".
 * @param {string} pluralForms  Plural-forms expression.
 * @return {Object} JSON payload.
 */
function buildJsonPayload( translations, locale, pluralForms ) {
	return {
		domain: 'messages',
		locale_data: {
			messages: {
				'': {
					domain: 'messages',
					plural_forms: pluralForms,
					lang: langCodeForLocale( locale ),
				},
				...translations,
			},
		},
	};
}

/**
 * @return {string} Current UTC timestamp in POT format.
 */
function utcDate() {
	const d = new Date();
	const pad = ( n ) => String( n ).padStart( 2, '0' );

	return (
		d.getUTCFullYear() +
		'-' +
		pad( d.getUTCMonth() + 1 ) +
		'-' +
		pad( d.getUTCDate() ) +
		' ' +
		pad( d.getUTCHours() ) +
		':' +
		pad( d.getUTCMinutes() ) +
		'+0000'
	);
}

/**
 * `extract` command: regenerate the .pot and report stale msgids per locale.
 */
function cmdExtract() {
	const msgids = collectSources();

	if ( msgids.size === 0 ) {
		console.error( '[i18n] No translatable string found in the sources.' );
		process.exit( 1 );
	}

	mkdirSync( LANG_DIR, { recursive: true } );

	const potPath = path.join( LANG_DIR, DOMAIN + '.pot' );
	writeFileSync( potPath, po.compile( buildPoTable( msgids ) ) );
	console.log(
		'[i18n] extract OK — ' +
			msgids.size +
			' msgid(s) → ' +
			path.basename( potPath )
	);

	for ( const locale of listLocales() ) {
		const poPath = path.join( LANG_DIR, DOMAIN + '-' + locale + '.po' );
		const existing = po.parse( readFileSync( poPath, 'utf8' ) );
		const missing = [];

		for ( const { context, msgid } of msgids.values() ) {
			const ctx = existing.translations[ context || '' ] || {};
			if (
				! ctx[ msgid ] ||
				! ctx[ msgid ].msgstr ||
				! ctx[ msgid ].msgstr[ 0 ]
			) {
				missing.push( context ? context + ' · ' + msgid : msgid );
			}
		}

		if ( missing.length ) {
			console.warn(
				'[i18n] ' +
					locale +
					': ' +
					missing.length +
					' msgid(s) untranslated:'
			);
			missing.forEach( ( msgid ) => console.warn( '  - ' + msgid ) );
		} else {
			console.log( '[i18n] ' + locale + ': all msgids are translated.' );
		}
	}
}

/**
 * Compile a single locale into .mo + the 3 JSON files, then validate.
 *
 * @param {string}      locale   Locale, e.g. "fr_FR".
 * @param {Set<string>} jsMsgids All JS source msgids, for the untranslated check.
 */
function compileLocale( locale, jsMsgids ) {
	const poPath = path.join( LANG_DIR, DOMAIN + '-' + locale + '.po' );

	if ( ! existsSync( poPath ) ) {
		console.error(
			'[i18n] Missing ' + path.basename( poPath ) + ' — cannot compile.'
		);
		return;
	}

	mkdirSync( LANG_DIR, { recursive: true } );

	const poData = po.parse( readFileSync( poPath, 'utf8' ) );
	const entries = poData.translations || {};
	const pluralForms = pluralFormsFor( poData );

	// .mo + round-trip check.
	const moBuf = mo.compile( poData );
	writeFileSync(
		path.join( LANG_DIR, DOMAIN + '-' + locale + '.mo' ),
		moBuf
	);
	mo.parse( moBuf );
	console.log(
		'[i18n] ' + locale + ' .mo OK → ' + DOMAIN + '-' + locale + '.mo'
	);

	// Split the translated strings per script handle, based on references.
	const jsonByHandle = {};

	for ( const [ ctx, ctxEntries ] of Object.entries( entries ) ) {
		for ( const [ msgid, entry ] of Object.entries( ctxEntries ) ) {
			if ( msgid === '' ) {
				continue;
			}

			const msgstr = (
				Array.isArray( entry.msgstr ) ? entry.msgstr : []
			).map( ( s ) => ( s ? s : '' ) );
			if ( ! msgstr.some( ( s ) => s ) ) {
				continue;
			}

			const reference =
				entry.comments && entry.comments.reference
					? entry.comments.reference
					: '';
			const handles = new Set();

			reference
				.split( /[\n,]+/ )
				.map( ( ref ) => ref.trim() )
				.filter( Boolean )
				.forEach( ( ref ) => {
					for ( const handle of handlesForFile( ref ) ) {
						handles.add( handle );
					}
				} );

			const key = ctx ? ctx + '\u0004' + msgid : msgid;

			for ( const handle of handles ) {
				if ( ! jsonByHandle[ handle ] ) {
					jsonByHandle[ handle ] = {};
				}
				jsonByHandle[ handle ][ key ] = msgstr;
			}
		}
	}

	for ( const [ handle, translations ] of Object.entries( jsonByHandle ) ) {
		const jsonPath = path.join(
			LANG_DIR,
			DOMAIN + '-' + locale + '-' + handle + '.json'
		);
		writeFileSync(
			jsonPath,
			JSON.stringify(
				buildJsonPayload( translations, locale, pluralForms ),
				null,
				'\t'
			) + '\n'
		);
		JSON.parse( readFileSync( jsonPath, 'utf8' ) );
		console.log(
			'[i18n] ' +
				locale +
				' JSON OK → ' +
				path.basename( jsonPath ) +
				' (' +
				Object.keys( translations ).length +
				' string(s))'
		);
	}

	// Validate: every JS source string must have a translation.
	let missing = 0;

	for ( const { context, msgid } of jsMsgids.values() ) {
		const ctxEntries = entries[ context || '' ] || {};
		const translation =
			ctxEntries[ msgid ] && Array.isArray( ctxEntries[ msgid ].msgstr )
				? ctxEntries[ msgid ].msgstr[ 0 ]
				: '';
		if ( ! translation ) {
			console.warn(
				'[i18n] ' +
					locale +
					' UNTRANSLATED JS string: "' +
					( context ? context + ' · ' : '' ) +
					msgid +
					'"'
			);
			missing++;
		}
	}

	console.log(
		'[i18n] ' +
			locale +
			' compile OK — .mo + ' +
			Object.keys( jsonByHandle ).length +
			' JSON file(s)' +
			( missing ? ', ' + missing + ' untranslated JS string(s)' : '' )
	);
}

/**
 * `compile` command: compile one locale or every language .po present.
 *
 * @param {string} [localeArg] Optional single locale to compile.
 */
function cmdCompile( localeArg ) {
	const locales = localeArg ? [ localeArg ] : listLocales();

	if ( ! locales.length ) {
		console.error(
			'[i18n] No ' +
				DOMAIN +
				'-{locale}.po found in ' +
				LANG_DIR +
				' — nothing to compile.'
		);
		process.exit( 1 );
	}

	const jsMsgids = collectSources( true );

	for ( const locale of locales ) {
		compileLocale( locale, jsMsgids );
	}
}

/**
 * `watch` command: auto-compile whenever a .po file is added or modified.
 */
function cmdWatch() {
	if ( ! existsSync( LANG_DIR ) ) {
		console.error( '[i18n] Missing ' + LANG_DIR + ' — create it.' );
		process.exit( 1 );
	}

	console.log(
		'[i18n] Watching ' + LANG_DIR + ' for .po changes… (Ctrl+C to stop)'
	);
	let timer = null;

	watch( LANG_DIR, ( eventType, filename ) => {
		if ( ! filename || ! filename.endsWith( '.po' ) ) {
			return;
		}

		const locale = localeFromFilename( filename );
		if ( ! locale ) {
			console.warn(
				'[i18n] Ignored ' +
					filename +
					' (expected ' +
					DOMAIN +
					'-{locale}.po)'
			);
			return;
		}

		if ( timer ) {
			clearTimeout( timer );
		}

		timer = setTimeout( () => {
			console.log(
				'[i18n] ' + filename + ' changed → compiling ' + locale
			);
			cmdCompile( locale );
		}, 300 );
	} );
}

const command = process.argv[ 2 ];
const localeArg = process.argv[ 3 ];

if ( command === 'extract' ) {
	cmdExtract();
} else if ( command === 'compile' ) {
	cmdCompile( localeArg );
} else if ( command === 'watch' ) {
	cmdWatch();
} else {
	console.error(
		'[i18n] Usage: node scripts/i18n.mjs <extract|compile [locale]|watch>'
	);
	process.exit( 1 );
}
