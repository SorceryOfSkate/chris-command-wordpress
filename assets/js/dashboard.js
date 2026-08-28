( function () {
	'use strict';

	const modules = {
		overview: {
			title: 'Overview',
			copy: 'Public interface online. Live signals and approved modules are ready.',
			detail: 'The public command layer is active and isolated from private Chris Command systems.',
		},
		news: {
			title: 'News',
			copy: 'Live intelligence is routed through WordPress and arranged inside the command surface.',
			detail: 'Seven approved RSS lanes are available through the public WordPress News service.',
		},
		projects: {
			title: 'Projects',
			copy: 'Approved public work can occupy this bay as modules are cleared for release.',
			detail: 'Project cards are visual placeholders until publishable material is approved.',
		},
		about: {
			title: 'About',
			copy: 'The public Chris Command surface presents selected work without exposing the private dashboard.',
			detail: 'This module is ready for public biography and mission content.',
		},
		contact: {
			title: 'Contact',
			copy: 'Public connection channels can be activated here after their destinations are approved.',
			detail: 'No personal address, private profile, or internal destination is embedded.',
		},
		settings: {
			title: 'Settings',
			copy: 'Local interface controls tune this browser without sending preferences elsewhere.',
			detail: 'Motion and panel controls remain browser-local.',
		},
	};

	const newsCategories = [
		{ slug: 'russia', label: 'Russia', code: 'RU', deck: 'Security, diplomacy, energy, and regional movement.' },
		{ slug: 'china', label: 'China', code: 'CN', deck: 'Trade, technology, Taiwan, and industrial policy.' },
		{ slug: 'north-korea', label: 'North Korea', code: 'NK', deck: 'Missile activity, sanctions, cyber operations, and regional security.' },
		{ slug: 'tech', label: 'Tech', code: 'AI', deck: 'AI systems, platform shifts, security incidents, chips, and product launches.' },
		{ slug: 'economics', label: 'Economics', code: '$', deck: 'Inflation, rates, labor, commodities, and market pressure.' },
		{ slug: 'united-states', label: 'United States', code: 'US', deck: 'Federal policy, national security, courts, and domestic affairs.' },
		{ slug: 'philippines', label: 'Philippines', code: 'PH', deck: 'National policy, regional security, markets, and infrastructure.' },
	];

	const spotifyTypes = new Set( [ 'album', 'artist', 'episode', 'playlist', 'show', 'track' ] );
	const spotifyStorageKey = 'chris-command-public:spotify-url:v1';
	const motionStorageKey = 'chris-command-public:reduced-motion:v1';

	function escapeMarkup( value ) {
		return String( value )
			.replaceAll( '&', '&amp;' )
			.replaceAll( '<', '&lt;' )
			.replaceAll( '>', '&gt;' )
			.replaceAll( '"', '&quot;' )
			.replaceAll( "'", '&#039;' );
	}

	function safeStorageGet( key ) {
		try {
			return window.localStorage.getItem( key ) || '';
		} catch ( error ) {
			return '';
		}
	}

	function safeStorageSet( key, value ) {
		try {
			if ( value ) {
				window.localStorage.setItem( key, value );
			} else {
				window.localStorage.removeItem( key );
			}
		} catch ( error ) {
			// The dashboard continues when storage is unavailable.
		}
	}

	function safeExternalUrl( value ) {
		try {
			const url = new URL( value );
			return [ 'http:', 'https:' ].includes( url.protocol ) ? url.href : '';
		} catch ( error ) {
			return '';
		}
	}

	function spotifyEmbedUrl( value ) {
		try {
			const url = new URL( value );
			const parts = url.pathname.split( '/' ).filter( Boolean );
			if ( url.hostname !== 'open.spotify.com' || parts.length < 2 || ! spotifyTypes.has( parts[ 0 ] ) ) {
				return '';
			}

			const identifier = parts[ 1 ];
			if ( ! /^[A-Za-z0-9]+$/.test( identifier ) ) {
				return '';
			}

			return `https://open.spotify.com/embed/${ parts[ 0 ] }/${ identifier }?theme=0`;
		} catch ( error ) {
			return '';
		}
	}

	function formatDate( value ) {
		const date = new Date( value );
		if ( Number.isNaN( date.getTime() ) ) {
			return 'Unknown time';
		}

		return new Intl.DateTimeFormat( undefined, {
			month: 'short',
			day: 'numeric',
			hour: 'numeric',
			minute: '2-digit',
		} ).format( date );
	}

	function updateClock( root ) {
		const now = new Date();
		const clock = root.querySelector( '[data-cc-clock]' );
		const weekday = root.querySelector( '[data-cc-weekday]' );
		const date = root.querySelector( '[data-cc-date]' );

		clock.textContent = new Intl.DateTimeFormat( undefined, {
			hour: '2-digit',
			minute: '2-digit',
			hour12: false,
		} ).format( now );
		clock.dateTime = now.toISOString();
		weekday.textContent = new Intl.DateTimeFormat( undefined, { weekday: 'long' } ).format( now );
		date.textContent = new Intl.DateTimeFormat( undefined, {
			month: 'short',
			day: '2-digit',
			year: 'numeric',
		} ).format( now );
	}

	function moduleIntro( kicker, title, copy, chip = '' ) {
		return `
			<header class="cc-module-intro">
				<div>
					<span class="cc-module-kicker">${ escapeMarkup( kicker ) }</span>
					<h2>${ escapeMarkup( title ) }</h2>
					<p>${ escapeMarkup( copy ) }</p>
				</div>
				${ chip ? `<span class="cc-module-chip">${ escapeMarkup( chip ) }</span>` : '' }
			</header>
		`;
	}

	function renderOverview( stage ) {
		stage.innerHTML = `
			${ moduleIntro( 'Public Command Layer', 'Interface Online', 'The redesigned dashboard shell is now owned by WordPress while approved services remain modular.', 'Operational' ) }
			<div class="cc-overview-grid">
				<article class="cc-metric"><span>Signal Lanes</span><strong>07</strong><small>Approved News categories</small></article>
				<article class="cc-metric"><span>Public Modules</span><strong>06</strong><small>Shell navigation points</small></article>
				<article class="cc-metric"><span>Private Exposure</span><strong>00</strong><small>Separated by design</small></article>
			</div>
			<div class="cc-signal-map">
				<div class="cc-radar"><strong>Public Signal Map</strong></div>
				<ul class="cc-briefing-list">
					<li><span>01</span><strong>WordPress shell active</strong><small>Online</small></li>
					<li><span>02</span><strong>Live News service connected</strong><small>Ready</small></li>
					<li><span>03</span><strong>Future modules isolated</strong><small>Standby</small></li>
					<li><span>04</span><strong>Private systems excluded</strong><small>Secure</small></li>
				</ul>
			</div>
		`;
	}

	function renderProjects( stage ) {
		stage.innerHTML = `
			${ moduleIntro( 'Approved Work Bay', 'Projects', 'This public-safe shell reserves space for selected projects without mirroring private project records.', 'Placeholder' ) }
			<div class="cc-placeholder-grid">
				<article class="cc-placeholder-card"><span>Public Module 01</span><h3>News Command</h3><p>Live, normalized headlines are already routed through WordPress.</p></article>
				<article class="cc-placeholder-card"><span>Public Module 02</span><h3>Selected Work</h3><p>Approved portfolio material can be published here in a later milestone.</p></article>
				<article class="cc-placeholder-card"><span>Public Module 03</span><h3>Release Log</h3><p>Public plugin releases can become a visible operational history.</p></article>
				<article class="cc-placeholder-card"><span>Approval Gate</span><h3>Private Boundary</h3><p>Nothing enters this bay until its content and dependencies are cleared for public use.</p></article>
			</div>
		`;
	}

	function renderAbout( stage ) {
		stage.innerHTML = `
			${ moduleIntro( 'Identity Channel', 'About Chris Command', 'A public command-center interface for selected work, live information, and future publishing.', 'Public Safe' ) }
			<div class="cc-placeholder-grid">
				<article class="cc-placeholder-card"><span>Mission</span><h3>Build With Focus</h3><p>Chris Command brings approved projects and signals into one recognizable operational surface.</p></article>
				<article class="cc-placeholder-card"><span>Architecture</span><h3>WordPress Owned</h3><p>WordPress provides the public frontend, persistence, APIs, integrations, and release path.</p></article>
				<article class="cc-placeholder-card"><span>Design</span><h3>Command Center</h3><p>The interface preserves the red HUD, dense information hierarchy, and focused control patterns of the source dashboard.</p></article>
				<article class="cc-placeholder-card"><span>Boundary</span><h3>Selective Publishing</h3><p>The private dashboard remains separate and may later send only explicitly approved content.</p></article>
			</div>
		`;
	}

	function renderContact( stage ) {
		stage.innerHTML = `
			${ moduleIntro( 'Connection Relay', 'Contact', 'Public contact destinations will activate only after they are explicitly approved for publication.', 'Standby' ) }
			<div class="cc-placeholder-grid">
				<article class="cc-placeholder-card"><span>Primary Relay</span><h3>Public Contact</h3><p>No private email address, profile URL, or internal destination is embedded in this release.</p></article>
				<article class="cc-placeholder-card"><span>Project Relay</span><h3>Collaboration</h3><p>A public-safe inquiry route can be connected here through WordPress later.</p></article>
				<article class="cc-placeholder-card"><span>Signal</span><h3>Status</h3><p>The interface is ready; external contact behavior remains intentionally inactive.</p></article>
				<article class="cc-placeholder-card"><span>Policy</span><h3>Approval Required</h3><p>Every published destination must pass the same public-boundary review as code and content.</p></article>
			</div>
		`;
	}

	function wireSettings( root, stage ) {
		const reduced = root.classList.contains( 'is-reduced-motion' );
		stage.innerHTML = `
			${ moduleIntro( 'Local Interface Control', 'Settings', 'These display controls stay in this browser and do not write WordPress records.', 'Browser Local' ) }
			<div class="cc-settings-grid">
				<div class="cc-setting-row"><div><strong>Reduced Motion</strong><p>Stops ambient radar, pulse, and folding animation.</p></div><button type="button" class="cc-settings-button" data-cc-motion aria-pressed="${ reduced ? 'true' : 'false' }">${ reduced ? 'Enabled' : 'Disabled' }</button></div>
				<div class="cc-setting-row"><div><strong>Selector Rail</strong><p>Fold or restore the left module and audio controls.</p></div><button type="button" class="cc-settings-button" data-cc-settings-selector>Toggle</button></div>
				<div class="cc-setting-row"><div><strong>Status Rail</strong><p>Fold or restore the right system-status controls.</p></div><button type="button" class="cc-settings-button" data-cc-settings-rail>Toggle</button></div>
			</div>
		`;

		stage.querySelector( '[data-cc-motion]' ).addEventListener( 'click', ( event ) => {
			const next = ! root.classList.contains( 'is-reduced-motion' );
			root.classList.toggle( 'is-reduced-motion', next );
			event.currentTarget.setAttribute( 'aria-pressed', String( next ) );
			event.currentTarget.textContent = next ? 'Enabled' : 'Disabled';
			safeStorageSet( motionStorageKey, next ? '1' : '' );
		} );
		stage.querySelector( '[data-cc-settings-selector]' ).addEventListener( 'click', () => toggleSelector( root ) );
		stage.querySelector( '[data-cc-settings-rail]' ).addEventListener( 'click', () => toggleRail( root ) );
	}

	function buildNewsShell( stage, activeSlug ) {
		const categoryButtons = newsCategories.map( ( category ) => `
			<button type="button" class="cc-news-category${ category.slug === activeSlug ? ' is-active' : '' }" data-cc-news-category="${ category.slug }" aria-pressed="${ category.slug === activeSlug ? 'true' : 'false' }">
				<span>${ escapeMarkup( category.code ) }</span>${ escapeMarkup( category.label ) }
			</button>
		` ).join( '' );

		stage.innerHTML = `
			<div class="cc-news-console">
				<nav class="cc-news-categories" aria-label="News categories">${ categoryButtons }</nav>
				<div class="cc-news-content">
					<header class="cc-news-header"><div><span class="cc-module-kicker">Live Intelligence Lane</span><h3 data-cc-news-title>Tech</h3><p data-cc-news-deck></p></div><span class="cc-news-freshness" data-cc-news-freshness>Connecting</span></header>
					<div class="cc-news-layout"><div class="cc-news-list" data-cc-news-list></div><article class="cc-news-detail" data-cc-news-detail></article></div>
				</div>
			</div>
		`;
	}

	function showNewsDetail( detail, article ) {
		detail.replaceChildren();
		const kicker = document.createElement( 'span' );
		kicker.textContent = 'Selected Signal';
		const heading = document.createElement( 'h4' );
		heading.textContent = article.title;
		const metadata = document.createElement( 'p' );
		metadata.textContent = `${ article.source } · ${ formatDate( article.publishedAt ) }`;
		const link = document.createElement( 'a' );
		const url = safeExternalUrl( article.url );
		link.textContent = 'Open original source ▶';
		link.target = '_blank';
		link.rel = 'noopener noreferrer';
		if ( url ) {
			link.href = url;
		} else {
			link.hidden = true;
		}
		detail.append( kicker, heading, metadata, link );
	}

	function showNewsLane( root, state, lane ) {
		const category = newsCategories.find( ( item ) => item.slug === state.newsCategory ) || newsCategories[ 3 ];
		const stage = root.querySelector( '[data-cc-stage]' );
		const title = stage.querySelector( '[data-cc-news-title]' );
		const deck = stage.querySelector( '[data-cc-news-deck]' );
		const freshness = stage.querySelector( '[data-cc-news-freshness]' );
		const list = stage.querySelector( '[data-cc-news-list]' );
		const detail = stage.querySelector( '[data-cc-news-detail]' );
		const status = root.querySelector( '[data-cc-news-status]' );

		title.textContent = category.label;
		deck.textContent = category.deck;
		list.replaceChildren();
		detail.replaceChildren();

		if ( ! lane || ! Array.isArray( lane.articles ) || lane.articles.length === 0 ) {
			list.innerHTML = '<p class="cc-error">No stories are available for this lane right now.</p>';
			detail.innerHTML = '<span>Signal unavailable</span><h4>Choose another News lane</h4><p>Available categories continue operating independently.</p>';
			freshness.textContent = 'Unavailable';
			status.textContent = 'Partial';
			return;
		}

		freshness.textContent = `${ lane.status === 'available' ? 'Live' : 'Cached' } · ${ formatDate( lane.fetchedAt ) }`;
		status.textContent = lane.status === 'available' ? 'Live' : 'Cached';

		lane.articles.slice( 0, 10 ).forEach( ( article, index ) => {
			const card = document.createElement( 'button' );
			card.type = 'button';
			card.className = `cc-news-card${ index === 0 ? ' is-active' : '' }`;
			const heading = document.createElement( 'strong' );
			heading.textContent = article.title;
			const metadata = document.createElement( 'small' );
			metadata.textContent = `${ article.source } · ${ formatDate( article.publishedAt ) }`;
			card.append( heading, metadata );
			card.addEventListener( 'click', () => {
				list.querySelectorAll( '.cc-news-card' ).forEach( ( item ) => item.classList.remove( 'is-active' ) );
				card.classList.add( 'is-active' );
				showNewsDetail( detail, article );
			} );
			list.append( card );
		} );

		showNewsDetail( detail, lane.articles[ 0 ] );
	}

	async function loadNewsLane( root, state ) {
		const stage = root.querySelector( '[data-cc-stage]' );
		const list = stage.querySelector( '[data-cc-news-list]' );
		const detail = stage.querySelector( '[data-cc-news-detail]' );
		const freshness = stage.querySelector( '[data-cc-news-freshness]' );
		const cacheKey = state.newsCategory;

		if ( state.newsCache.has( cacheKey ) ) {
			showNewsLane( root, state, state.newsCache.get( cacheKey ) );
			return;
		}

		list.innerHTML = '<div class="cc-loading">Receiving live signals</div>';
		detail.innerHTML = '<span>Signal briefing</span><h4>Connecting to WordPress News</h4><p>The selected RSS lane is being normalized and checked.</p>';
		freshness.textContent = 'Connecting';

		try {
			const endpoint = new URL( root.dataset.newsEndpoint, window.location.href );
			endpoint.searchParams.set( 'category', state.newsCategory );
			const response = await window.fetch( endpoint, { headers: { Accept: 'application/json' } } );
			if ( ! response.ok ) {
				throw new Error( 'News request failed.' );
			}
			const lane = await response.json();
			state.newsCache.set( cacheKey, lane );
			showNewsLane( root, state, lane );
		} catch ( error ) {
			showNewsLane( root, state, null );
		}
	}

	function renderNews( root, state, stage ) {
		buildNewsShell( stage, state.newsCategory );
		stage.querySelectorAll( '[data-cc-news-category]' ).forEach( ( button ) => {
			button.addEventListener( 'click', () => {
				state.newsCategory = button.dataset.ccNewsCategory;
				stage.querySelectorAll( '[data-cc-news-category]' ).forEach( ( item ) => {
					const active = item === button;
					item.classList.toggle( 'is-active', active );
					item.setAttribute( 'aria-pressed', String( active ) );
				} );
				loadNewsLane( root, state );
			} );
		} );
		loadNewsLane( root, state );
	}

	function renderModule( root, state, slug ) {
		const module = modules[ slug ] || modules.overview;
		const stage = root.querySelector( '[data-cc-stage]' );
		state.activeModule = slug;
		root.querySelector( '[data-cc-active-title]' ).textContent = module.title;
		root.querySelector( '[data-cc-active-copy]' ).textContent = module.copy;
		root.querySelector( '[data-cc-current-lane]' ).textContent = module.title;
		root.querySelector( '[data-cc-current-detail]' ).textContent = module.detail;

		root.querySelectorAll( '[data-cc-module]' ).forEach( ( button ) => {
			const active = button.dataset.ccModule === slug;
			button.classList.toggle( 'is-active', active );
			button.setAttribute( 'aria-pressed', String( active ) );
		} );

		if ( slug === 'news' ) {
			renderNews( root, state, stage );
		} else if ( slug === 'projects' ) {
			renderProjects( stage );
		} else if ( slug === 'about' ) {
			renderAbout( stage );
		} else if ( slug === 'contact' ) {
			renderContact( stage );
		} else if ( slug === 'settings' ) {
			wireSettings( root, stage );
		} else {
			renderOverview( stage );
		}
	}

	function toggleSelector( root ) {
		const folded = ! root.classList.contains( 'is-selector-folded' );
		root.classList.toggle( 'is-selector-folded', folded );
		const button = root.querySelector( '[data-cc-selector-toggle]' );
		button.setAttribute( 'aria-expanded', String( ! folded ) );
		button.lastChild.textContent = folded ? ' Restore selector' : ' Fold selector';
		button.querySelector( 'span' ).textContent = folded ? '▶' : '◀';
	}

	function toggleRail( root ) {
		const folded = ! root.classList.contains( 'is-rail-folded' );
		root.classList.toggle( 'is-rail-folded', folded );
		const button = root.querySelector( '[data-cc-rail-toggle]' );
		button.setAttribute( 'aria-expanded', String( ! folded ) );
	}

	function wireWheel( root, state ) {
		const wheel = root.querySelector( '[data-cc-wheel]' );
		const buttons = Array.from( root.querySelectorAll( '[data-cc-module]' ) );
		let wheelLocked = false;

		buttons.forEach( ( button ) => {
			button.addEventListener( 'click', () => renderModule( root, state, button.dataset.ccModule ) );
		} );

		function selectRelative( direction ) {
			const current = Math.max( 0, buttons.findIndex( ( button ) => button.dataset.ccModule === state.activeModule ) );
			const next = ( current + direction + buttons.length ) % buttons.length;
			renderModule( root, state, buttons[ next ].dataset.ccModule );
			buttons[ next ].focus( { preventScroll: true } );
			buttons[ next ].scrollIntoView( { block: 'nearest', inline: 'nearest' } );
		}

		wheel.addEventListener( 'keydown', ( event ) => {
			if ( [ 'ArrowDown', 'ArrowRight' ].includes( event.key ) ) {
				event.preventDefault();
				selectRelative( 1 );
			} else if ( [ 'ArrowUp', 'ArrowLeft' ].includes( event.key ) ) {
				event.preventDefault();
				selectRelative( -1 );
			} else if ( event.key === 'Enter' ) {
				event.preventDefault();
				renderModule( root, state, state.activeModule );
			}
		} );

		wheel.addEventListener( 'wheel', ( event ) => {
			if ( wheelLocked || Math.abs( event.deltaY ) < 8 ) {
				return;
			}
			event.preventDefault();
			wheelLocked = true;
			selectRelative( event.deltaY > 0 ? 1 : -1 );
			window.setTimeout( () => { wheelLocked = false; }, 180 );
		}, { passive: false } );
	}

	function wireTimer( root, state ) {
		const display = root.querySelector( '[data-cc-timer]' );
		const toggle = root.querySelector( '[data-cc-timer-toggle]' );
		const reset = root.querySelector( '[data-cc-timer-reset]' );

		function paint() {
			const minutes = Math.floor( state.timerSeconds / 60 );
			const seconds = state.timerSeconds % 60;
			display.textContent = `${ String( minutes ).padStart( 2, '0' ) }:${ String( seconds ).padStart( 2, '0' ) }`;
			toggle.firstChild.textContent = state.timerRunning ? 'Pause Focus ' : 'Start Focus ';
		}

		function stop() {
			state.timerRunning = false;
			if ( state.timerId ) {
				window.clearInterval( state.timerId );
				state.timerId = 0;
			}
			paint();
		}

		toggle.addEventListener( 'click', () => {
			if ( state.timerRunning ) {
				stop();
				return;
			}
			state.timerRunning = true;
			state.timerId = window.setInterval( () => {
				state.timerSeconds = Math.max( 0, state.timerSeconds - 1 );
				if ( state.timerSeconds === 0 ) {
					stop();
				}
				paint();
			}, 1000 );
			paint();
		} );

		reset.addEventListener( 'click', () => {
			stop();
			state.timerSeconds = 25 * 60;
			paint();
		} );
	}

	function wireAudio( root ) {
		const form = root.querySelector( '[data-cc-audio-form]' );
		const input = root.querySelector( '[data-cc-spotify-input]' );
		const frame = root.querySelector( '[data-cc-spotify-frame]' );
		const display = root.querySelector( '[data-cc-audio-display]' );
		const status = root.querySelector( '[data-cc-audio-status]' );

		function load( value, persist = true ) {
			const embed = spotifyEmbedUrl( value );
			if ( ! embed ) {
				status.textContent = 'Use a valid public Spotify track, album, playlist, artist, show, or episode URL.';
				return;
			}

			const iframe = document.createElement( 'iframe' );
			iframe.src = embed;
			iframe.title = 'Spotify player';
			iframe.loading = 'lazy';
			iframe.allow = 'autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture';
			frame.replaceChildren( iframe );
			frame.hidden = false;
			display.hidden = true;
			input.value = value;
			status.textContent = 'Audio channel loaded for this browser.';
			if ( persist ) {
				safeStorageSet( spotifyStorageKey, value );
			}
		}

		form.addEventListener( 'submit', ( event ) => {
			event.preventDefault();
			load( input.value.trim() );
		} );

		root.querySelector( '[data-cc-audio-reset]' ).addEventListener( 'click', () => {
			frame.replaceChildren();
			frame.hidden = true;
			display.hidden = false;
			input.value = '';
			status.textContent = 'Audio channel reset. No link is stored.';
			safeStorageSet( spotifyStorageKey, '' );
		} );

		const saved = safeStorageGet( spotifyStorageKey );
		if ( saved ) {
			load( saved, false );
		}
	}

	function initDashboard( root ) {
		if ( root.dataset.ccReady === 'true' ) {
			return;
		}
		root.dataset.ccReady = 'true';

		const state = {
			activeModule: 'overview',
			newsCategory: 'tech',
			newsCache: new Map(),
			timerSeconds: 25 * 60,
			timerRunning: false,
			timerId: 0,
		};

		if ( safeStorageGet( motionStorageKey ) === '1' ) {
			root.classList.add( 'is-reduced-motion' );
		}
		const compactLayout = window.matchMedia( '(max-width: 1180px)' );
		if ( compactLayout.matches ) {
			root.classList.add( 'is-rail-folded' );
		}
		compactLayout.addEventListener( 'change', ( event ) => {
			if ( event.matches ) {
				root.classList.add( 'is-rail-folded' );
			}
		} );

		updateClock( root );
		window.setInterval( () => updateClock( root ), 30000 );
		wireWheel( root, state );
		wireTimer( root, state );
		wireAudio( root );
		root.querySelector( '[data-cc-selector-toggle]' ).addEventListener( 'click', () => toggleSelector( root ) );
		root.querySelector( '[data-cc-rail-toggle]' ).addEventListener( 'click', () => toggleRail( root ) );
		root.querySelector( '[data-cc-rail-reopen]' ).addEventListener( 'click', () => toggleRail( root ) );
		renderModule( root, state, 'overview' );
	}

	function boot() {
		document.querySelectorAll( '[data-cc-dashboard]' ).forEach( initDashboard );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
