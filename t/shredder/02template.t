#!/usr/bin/perl -w

use strict;
use warnings;

use RT::Test; use Test::More;
use Test::Deep;
BEGIN { require "t/shredder/utils.pl"; }
init_db();

plan tests => 7;

diag 'global template' if $ENV{'TEST_VERBOSE'};
{
	create_savepoint('clean');
    my $template = RT::Model::Template->new(current_user => RT->system_user );
    my ($id, $msg) = $template->create(
        name => 'my template',
        Content => "\nsome content",
    );
    ok($id, 'Created template') or diag "error: $msg";

	my $shredder = shredder_new();
	$shredder->put_objects( Objects => $template );
	$shredder->wipeout_all;
	cmp_deeply( dump_current_and_savepoint('clean'), "current DB equal to savepoint");
}

diag 'local template' if $ENV{'TEST_VERBOSE'};
{
	create_savepoint('clean');
    my $template = RT::Model::Template->new(current_user => RT->system_user );
    my ($id, $msg) = $template->create(
        name => 'my template',
        Queue => 'General',
        Content => "\nsome content",
    );
    ok($id, 'Created template') or diag "error: $msg";

	my $shredder = shredder_new();
	$shredder->put_objects( Objects => $template );
	$shredder->wipeout_all;
	cmp_deeply( dump_current_and_savepoint('clean'), "current DB equal to savepoint");
}

diag 'template used in scrip' if $ENV{'TEST_VERBOSE'};
{
	create_savepoint('clean');
    my $template = RT::Model::Template->new(current_user => RT->system_user );
    my ($id, $msg) = $template->create(
        name => 'my template',
        Queue => 'General',
        Content => "\nsome content",
    );
    ok($id, 'Created template') or diag "error: $msg";

    my $scrip = RT::Model::Scrip->new(current_user => RT->system_user );
    ($id, $msg) = $scrip->create(
        description    => 'my scrip',
        Queue          => 'General',
        ScripCondition => 'On Create',
        ScripAction    => 'Open Tickets',
        Template       => $template->id,
    );
    ok($id, 'Created scrip') or diag "error: $msg";

	my $shredder = shredder_new();
	$shredder->put_objects( Objects => $template );
	$shredder->wipeout_all;
	cmp_deeply( dump_current_and_savepoint('clean'), "current DB equal to savepoint");
}

if( is_all_successful() ) {
	cleanup_tmp();
} else {
	diag( note_on_fail() );
}

