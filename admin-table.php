<?php
//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Link_List_Table extends WP_List_Table {

    function __construct() {
        parent::__construct( array(
            'singular'=> 'wp_list_text_link',
            'plural' => 'wp_list_test_links',
            'ajax'   => false
        ) );
    }
    
    function extra_tablenav( $which ) {
        if ( $which == "top" ){
            
        echo"";
        }
        if ( $which == "bottom" ){
            
        echo"";
        }
    }

    function get_columns() {
        return $columns= array(
            'id'=>__('ID'),
            'fname'=>__('First Name'),
            'lname'=>__('Last Name'),
            'email'=>__('Email'),
            'dob'=>__('DOB'),
            'phone'=>__('Phone'),
            'website'=>__('Website'),
            'date'=>__('Created on')
        );
    }
    
    public function get_sortable_columns() {
        return $sortable = array(
            'id'=>array('id',true),
            'fname'=>array("fname",true),
            'lname'=>array("lname",true),
            'email'=>array("email",true),
            'dob'=>array("dob",true),
            'phone'=>array("phone",true),
            'website'=>array("website",true),
            'date'=>array("date",true)
        );
    }
    
    function prepare_items() {
        global $wpdb;
        $screen = get_current_screen();
        
        $query = "SELECT * FROM ".$wpdb->prefix."wrg_subscribe_entries";
        
        $orderby = !empty($_GET["orderby"]) ? $_GET["orderby"] : 'ASC';
        $order = !empty($_GET["order"]) ? $_GET["order"] : "";
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

        $totalitems = $wpdb->query($query);
        
        $perpage = 25;
        
        $paged = !empty($_GET["paged"]) ? $_GET["paged"] : "";
        
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        
        $totalpages = ceil($totalitems/$perpage);
        
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }
        
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $hidden = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $wpdb->get_results($query);
    }

    function display_rows() {
        $records = $this->items;
        $columns = $this->get_columns();
        $hidden = array();
        if(!empty($records)){
            foreach($records as $rec){
                echo '<tr id="record_'.$rec->id.'">';
                foreach ( $columns as $column_name => $column_display_name ) {
                    $class = "class='$column_name column-$column_name'";
                    $style = "";
                    if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
                    $attributes = $class . $style;
                    switch ( $column_name ) {
                        case "id":  echo '<td '.$attributes.'>'.stripslashes($rec->id).'</td>';   break;
                        case "fname": echo '<td '.$attributes.'>'.stripslashes($rec->fname).'</td>'; break;
                        case "lname": echo '<td '.$attributes.'>'.stripslashes($rec->lname).'</td>'; break;
                        case "email": echo '<td '.$attributes.'>'.$rec->email.'</td>'; break;
                        case "dob": echo '<td '.$attributes.'>'.$rec->dob.'</td>'; break;
                        case "phone": echo '<td '.$attributes.'>'.$rec->phone.'</td>'; break;
                        case "website": echo '<td '.$attributes.'>'.$rec->website.'</td>'; break;
                        case "date": echo '<td '.$attributes.'>'.$rec->date.'</td>'; break;
                    }
                }
            echo'</tr>';
            }
        }
    }
}
$wp_list_table = new Link_List_Table();
$wp_list_table->prepare_items();
$wp_list_table->search_box('Search', 'search');
$wp_list_table->display();