const wpTextdomain = require( 'wp-textdomain' );

wpTextdomain( process.argv[ 2 ], {
	domain: 'paiementpro-for-give',
	fix: true,
} );
