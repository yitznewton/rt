use warnings;
use strict;

use RT::Test nodata => 1, tests => 5;
use RT::I18N;
use Encode;

my @warnings;
local $SIG{__WARN__} = sub {
    push @warnings, "@_";
};

diag "normal mime encoding conversion: utf8 => iso-8859-1";
{
    my $mime = MIME::Entity->build(
        Type => 'text/plain; charset=utf-8',
        Data => ['À中文'],
    );

    RT::I18N::SetMIMEEntityToEncoding( $mime, 'iso-8859-1', );
    like(
        join( '', @warnings ),
        qr/does not map to iso-8859-1/,
        'get no-map warning'
    );
    is( $mime->stringify_body, 'À中文', 'body is not changed' );
    is( $mime->head->mime_attr('Content-Type'), 'application/octet-stream' );
    @warnings = ();
}

diag "mime encoding conversion: utf8 => iso-8859-1";
{
    my $mime = MIME::Entity->build(
        Type => 'text/plain; charset=utf-8',
        Data => ['À中文'],
    );
    RT::I18N::SetMIMEEntityToEncoding( $mime, 'iso-8859-1', '', 1 );
    is( scalar @warnings, 0, 'no warnings with force' );
    is( $mime->stringify_body, 'À中文', 'body is not changed' );
}

