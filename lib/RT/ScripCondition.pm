


# Autogenerated by DBIx::SearchBuilder factory (by <jesse@bestpractical.com>)
# WARNING: THIS FILE IS AUTOGENERATED. ALL CHANGES TO THIS FILE WILL BE LOST.  
# 
# !! DO NOT EDIT THIS FILE !!
#


=head1 NAME

RT::ScripCondition


=head1 SYNOPSIS

=head1 DESCRIPTION

=head1 METHODS

=cut

package RT::ScripCondition;
use RT::Record; 


use vars qw( @ISA );
@ISA= qw( RT::Record );

sub _Init {
  my $self = shift; 

  $self->Table('ScripConditions');
  $self->SUPER::_Init(@_);
}





=item Create PARAMHASH

Create takes a hash of values and creates a row in the database:

  varchar(255) 'Name'.
  varchar(255) 'Description'.
  varchar(60) 'ExecModule'.
  varchar(255) 'Argument'.
  varchar(60) 'ApplicableTransTypes'.

=cut




sub Create {
    my $self = shift;
    my %args = ( 
                Name => '',
                Description => '',
                ExecModule => '',
                Argument => '',
                ApplicableTransTypes => '',

		  @_);
    $self->SUPER::Create(
                         Name => $args{'Name'},
                         Description => $args{'Description'},
                         ExecModule => $args{'ExecModule'},
                         Argument => $args{'Argument'},
                         ApplicableTransTypes => $args{'ApplicableTransTypes'},
);

}



=item id

Returns the current value of id. 
(In the database, id is stored as int(11).)


=cut


=item Name

Returns the current value of Name. 
(In the database, Name is stored as varchar(255).)



=item SetName VALUE


Set Name to VALUE. 
Returns (1, 'Status message') on success and (0, 'Error Message') on failure.
(In the database, Name will be stored as a varchar(255).)


=cut


=item Description

Returns the current value of Description. 
(In the database, Description is stored as varchar(255).)



=item SetDescription VALUE


Set Description to VALUE. 
Returns (1, 'Status message') on success and (0, 'Error Message') on failure.
(In the database, Description will be stored as a varchar(255).)


=cut


=item ExecModule

Returns the current value of ExecModule. 
(In the database, ExecModule is stored as varchar(60).)



=item SetExecModule VALUE


Set ExecModule to VALUE. 
Returns (1, 'Status message') on success and (0, 'Error Message') on failure.
(In the database, ExecModule will be stored as a varchar(60).)


=cut


=item Argument

Returns the current value of Argument. 
(In the database, Argument is stored as varchar(255).)



=item SetArgument VALUE


Set Argument to VALUE. 
Returns (1, 'Status message') on success and (0, 'Error Message') on failure.
(In the database, Argument will be stored as a varchar(255).)


=cut


=item ApplicableTransTypes

Returns the current value of ApplicableTransTypes. 
(In the database, ApplicableTransTypes is stored as varchar(60).)



=item SetApplicableTransTypes VALUE


Set ApplicableTransTypes to VALUE. 
Returns (1, 'Status message') on success and (0, 'Error Message') on failure.
(In the database, ApplicableTransTypes will be stored as a varchar(60).)


=cut


=item Creator

Returns the current value of Creator. 
(In the database, Creator is stored as int(11).)


=cut


=item Created

Returns the current value of Created. 
(In the database, Created is stored as datetime.)


=cut


=item LastUpdatedBy

Returns the current value of LastUpdatedBy. 
(In the database, LastUpdatedBy is stored as int(11).)


=cut


=item LastUpdated

Returns the current value of LastUpdated. 
(In the database, LastUpdated is stored as datetime.)


=cut



sub _ClassAccessible {
    {
     
        id =>
		{read => 1, type => 'int(11)', default => ''},
        Name => 
		{read => 1, write => 1, type => 'varchar(255)', default => ''},
        Description => 
		{read => 1, write => 1, type => 'varchar(255)', default => ''},
        ExecModule => 
		{read => 1, write => 1, type => 'varchar(60)', default => ''},
        Argument => 
		{read => 1, write => 1, type => 'varchar(255)', default => ''},
        ApplicableTransTypes => 
		{read => 1, write => 1, type => 'varchar(60)', default => ''},
        Creator => 
		{read => 1, auto => 1, type => 'int(11)', default => ''},
        Created => 
		{read => 1, auto => 1, type => 'datetime', default => ''},
        LastUpdatedBy => 
		{read => 1, auto => 1, type => 'int(11)', default => ''},
        LastUpdated => 
		{read => 1, auto => 1, type => 'datetime', default => ''},

 }
};


        eval "require RT::ScripCondition_Overlay";
        if ($@ && $@ !~ /^Can't locate/) {
            die $@;
        };

        eval "require RT::ScripCondition_Local";
        if ($@ && $@ !~ /^Can't locate/) {
            die $@;
        };




=head1 SEE ALSO

This class allows "overlay" methods to be placed
into the following files _Overlay is for a System overlay by the original author,
while _Local is for site-local customizations.  

These overlay files can contain new subs or subs to replace existing subs in this module.

If you'll be working with perl 5.6.0 or greater, each of these files should begin with the line 

   no warnings qw(redefine);

so that perl does not kick and scream when you redefine a subroutine or variable in your overlay.

RT::ScripCondition_Overlay, RT::ScripCondition_Local

=cut


1;
