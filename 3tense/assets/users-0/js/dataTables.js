function table_initialize() {
    // Setup - add a text input to each footer cell
    $('.table tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" style="max-width: 100px;" placeholder="Search '+title+'" />' );
    } );

    // remove searchbox from the Action column
    $('.action-col input').remove();

    // for reminder tables search
    if(typeof table_rem != 'undefined') {
        table_rem.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );
    }
    // for your reminder tables search
    if(typeof table_rem_p != 'undefined') {
        table_rem_p.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );
    }

    // Apply the search
    if(typeof table != 'undefined') {
        table.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                //event.preventDefault();
                //table.fnFilter( this.value, $("thead input").index(this) );
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );
    }

    // Apply the search
    if(typeof table_val != 'undefined') {
        table_val.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                //event.preventDefault();
                //table.fnFilter( this.value, $("thead input").index(this) );
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );
    }


}