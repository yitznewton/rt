package RT::Action::TicketUpdateDates;
use strict;
use warnings;

use base qw/Jifty::Action::Record::Update/;

sub record_class { 'RT::Model::Ticket' }

=head2 take_action

=cut

sub take_action {
    my $self        = shift;
    my @date_fields = qw/told starts started due/;

    foreach my $field (@date_fields) {
        my $value = $self->argument_value($field);
        if ( defined $value ) {
            my $date = RT::Date->new();
            $date->set(
                format => 'unknown',
                value  => $value,
            );

# the date is not real utc, we set it as utc to get rid of user timezone
# convert, since record->$obj already get converted, it's wrong to convert
# it too.
            my $fake_utc_date = RT::Date->new();
            $fake_utc_date->set(
                format => 'unknown',
                value  => $value,
                timezone => 'UTC',
            );

            my $obj = $field . '_obj';
            if ( $fake_utc_date->unix != $self->record->$obj()->unix() ) {
                Jifty->log->error( $date->iso, ' ', $self->record->$obj->iso
                        );
                my $set = "set_$field";
                my ( $status, $msg ) = $self->record->$set( $date->iso );
                unless ($status) {
                    $self->result->failure(
                        _( 'Update [_1] failed: [_2]', $field, $msg ) );
                    last;
                }
            }
        }
    }

    $self->report_success unless $self->result->failure;
    return 1;
}

sub report_success {
    my $self = shift;

    # Your success message here
    $self->result->message( _('Dates Updated') );
}

1;
