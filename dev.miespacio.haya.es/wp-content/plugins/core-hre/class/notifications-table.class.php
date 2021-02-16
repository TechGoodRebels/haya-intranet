<?php

class Core_HRE_Notifications extends WP_List_Table
{
	public function __construct()
	{
		parent::__construct( array(
			'singular' => __( 'Aviso', 'core-hre' ),
			'plural'   => __( 'Avisos', 'core-hre' ),
			'ajax'     => false,
		) );
		
		if( isset( $_REQUEST['deleted'] ) && $_REQUEST['deleted'] == 'success' ) {
			add_action( 'admin_notices', array( $this, 'notification_deleted_notice' ) );
		} elseif ( isset( $_REQUEST['deleted'] ) && $_REQUEST['deleted'] == 'error' ) {
			add_action( 'admin_notices', array( $this, 'notification_deleted_error' ) );
		}
	}
	
	/**
	* Retrieve bookings data from the database
	*
	* @param int $per_page
	* @param int $page_number
	*
	* @return mixed
	*/
	public static function get_notifications( $per_page = 20, $page_number = 1 ) 
	{
		
		$args = array(
			'per_page' 		=> $per_page,
			'page_number' 	=> $page_number,
		);
		
		$search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		
		if( isset( $_REQUEST['order']) )
			$args['order'] = $_REQUEST['order'];
		if( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];
		if( isset( $_REQUEST['s'] ) )
			$args['search'] = $search_key;

		$notifications = self::wpdb_query_notifications( $args );

		return $notifications;
	}

	public static function wpdb_query_notifications( $args )
	{
		$args = shortcode_atts( array(
			'per_page' 		=> 0,
			'page_number' 	=> 1,
			'order' 		=> 'ASC',
			'orderby' 		=> 'notification_date',
			'search' 		=> '',
		), $args );

		global $wpdb;
		
		$query = '';
		$queryArgs = array();
		
		$query .= 
		'SELECT DISTINCT um.content,
			um.id AS notification_id,
			um.time AS notification_date,
			um.content AS notification_content,
			um.url AS notification_link
		FROM 
			' . $wpdb->prefix . 'um_notifications AS um
		WHERE um.type = "custom_notifications" ';
		
		if( $args['search'] )
		{
			$query .= 
			' AND um.content LIKE "%' . $args['search'] . '%"';
		}

		$query .= ' GROUP BY um.content';
		
		$order = ( $args && strtolower( $args['order'] ) != 'desc' ? 'ASC' : 'DESC' );
		
		if( $args['orderby'] )
		{
			switch( $args['orderby'] )
			{
				case 'notification_id':
					$query .= ' ORDER BY um.id ' . $order;
					break;
				case 'notification_date':
					$query .= ' ORDER BY um.time ' . $order;
					break;
				case 'notification_content':
					$query .= ' ORDER BY um.content ' . $order;
					break;
				case 'notification_link':
					$query .= ' ORDER BY um.url ' . $order;
					break;
			}
		}
		else
		{
			$query .= ' ORDER BY um.time ' . $order;
		}
		
		if( $args['per_page'] )
		{
			$query .= ' LIMIT %d';
			$queryArgs[] = $args['per_page'];
		}
		
		if( $offset = ( $args['page_number'] - 1 ) * $args['per_page'] )
		{
			$query .= ' OFFSET %d';
			$queryArgs[] = $offset;
		}
		
		$query  = $wpdb->prepare( $query, $queryArgs );
		$result = $wpdb->get_results( $query, 'ARRAY_A' );

		return $result;
	}
	
	/**
	* Delete a notification record.
	*
	* @param int $id notification ID
	*/
	public static function delete_notification( $id ) {
		global $wpdb;

		$actionDelete = false;

		$query  = $wpdb->prepare( 'SELECT * FROM `' . $wpdb->prefix . 'um_notifications` WHERE id=%d', $id );
		$result = $wpdb->get_results( $query );

		if( isset( $result ) ) {
			$queryDelete  = $wpdb->prepare( 'DELETE FROM `' . $wpdb->prefix . 'um_notifications` WHERE content = %s AND url = %s AND type = %s', $result[0]->content, $result[0]->url, 'custom_notifications' );
			$actionDelete = $wpdb->query( $queryDelete );
		}

		return $actionDelete;
	}
	
	public function notification_deleted_notice() {
		$output  = '';
		$output .= 
			'<div class="notice notice-success is-dismissible">
				<p>' . __( 'Aviso eliminado. Se borrará para todos los usuarios.', 'core-hre' ) . '</p>
			</div>';

		echo $output;
	}

	public function notification_deleted_error() {
		$output  = '';
		$output .= 
			'<div class="notice notice-error is-dismissible">
				<p>' . __( 'Fallo al eliminar uno o más avisos. Vuelva a intentarlo o contacte con el administrador.', 'core-hre' ) . '</p>
			</div>';

		echo $output;
	}
	
	/**
	* Returns the count of records in the database.
	*
	* @return null|string
	*/
	public static function record_count() {

		global $wpdb;
		$query = 'SELECT DISTINCT content, COUNT(*) FROM ' . $wpdb->prefix . 'um_notifications WHERE type = "custom_notifications"';

		return $wpdb->get_var( $query );
	}
   
	/** Text displayed when no booking data is available */
	public function no_items() {
		_e( 'No se han encontrado avisos.', 'core-hre' );
	}
	
	/**
	* Method for name column
	*
	* @param array $item an array of DB data
	*
	* @return string
	*/
	public function column_notification_content( $item ) 
	{
		$delete_nonce = wp_create_nonce( 'core_delete_notification' );

		$actions = array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&notification=%s&_wpnonce=%s">Borrar</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['notification_id'] ), $delete_nonce ),
		);

		return $this->row_actions( $actions );
   }
   
   /**
	* Render a column when no column specific method exists.
	*
	* @param array $item
	* @param string $column_name
	*
	* @return mixed
	*/
	public function column_default( $item, $column_name )
	{
		switch( $column_name )
		{	
			case 'cb':
				return $item['notification_id'];
			case 'notification_content':
				return $item['notification_content'];
			case 'notification_date':
				return $item['notification_datetime'];
			case 'notification_link':
				return $item['notification_link'];
			default:
      			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}
	
	/**
	* Render the bulk edit checkbox
	*
	* @param array $item
	*
	* @return string
	*/
	function column_cb( $item )
	{
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['notification_id']
		);
	}
	
	/**
	*  Associative array of columns
	*
	* @return array
	*/
	function get_columns()
	{	
		// Declarar columnas customizadas
		$columns = array(
			'cb' 	    			=> '<input type="checkbox" />',
			'notification_content'	=> __( 'Contenido', 'core-hre' ),
			'notification_date' 	=> __( 'Fecha envío', 'core-hre' ),
			'notification_link'		=> __( 'Enlace', 'core-hre' )
		);
		
		return $columns;
	}

	/**
	* Columns to make sortable.
	*
	* @return array
	*/
	public function get_sortable_columns()
	{	
		$sortable_columns = array(
			'notification_content'	=> array( 'notification_content', true ),
			'notification_date' 	=> array( 'notification_date', true ),
			'notification_link'    	=> array( 'notification_link', true ),
		);
		return $sortable_columns;
	}

	/**
	* Returns an associative array containing the bulk action
	*
	* @return array
	*/
	public function get_bulk_actions()
	{
		$actions = array(
			'bulk-delete' => 'Delete',
		);
		return $actions;
	}

	/**
	* Pintamos input de busqueda
	*
	* @return empty
	*/
	public function search_box( $text, $input_id ) 
	{ 
		?>
	    <p class="search-box">
	    	<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
	    	<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php echo $text; ?>" />
	    	<?php submit_button( 'Buscar', 'button', false, false, array( 'id' => 'search-submit' ) ); ?>
	  	</p>
		<?php 
	}
	
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items()
    {
    	$this->_column_headers = $this->get_column_info();

        $columns 	= $this->get_columns();
        $hidden 	= $this->get_hidden_columns();
        $sortable 	= $this->get_sortable_columns();

        $perPage 	 = 20;
        $currentPage = $this->get_pagenum();

        $this->process_bulk_action();

        $data = self::get_notifications( $perPage, $currentPage );

        $totalItems  = count( $data );

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

	public function process_bulk_action()
	{
		if( 'delete' === $this->current_action() )
		{
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if( !wp_verify_nonce( $nonce, 'core_delete_notification' ) )
			{
				die( __( 'No tienes permisos para esta acción.', 'core-hre' ) );
			}
			else
			{
				$delete = self::delete_notification( absint( $_GET['notification'] ) );

				if( $delete ) {
					$redirectUrl = add_query_arg( array(
						'page' 		=> 'core_notifications_list',
						'deleted' 	=> 'success',
					), get_admin_url( get_current_blog_id(), 'admin.php' ) );
				} else {
					$redirectUrl = add_query_arg( array(
						'page' 		=> 'core_notifications_list',
						'deleted' 	=> 'error',
					), get_admin_url( get_current_blog_id(), 'admin.php' ) );
				}

				wp_redirect( $redirectUrl );

				exit;
			}
		}

		// If the delete bulk action is triggered
		if( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
			 || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) )
		{
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			$isSuccess  = true;

			foreach( $delete_ids as $id )
			{
				$delete = self::delete_notification( $id );

				if( !$delete ) $isSuccess = false;
			}

			if( $isSuccess ) {
				$redirectUrl = add_query_arg( array(
					'page' 		=> 'core_notifications_list',
					'deleted' 	=> 'success',
				), get_admin_url( get_current_blog_id(), 'admin.php' ) );
			} else {
				$redirectUrl = add_query_arg( array(
					'page' 		=> 'core_notifications_list',
					'deleted' 	=> 'error',
				), get_admin_url( get_current_blog_id(), 'admin.php' ) );
			}

			wp_redirect( $redirectUrl );

			exit;
		}
	}

	/**
 	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	public function display_rows() {

	   // Get the records registered in the prepare_items method
	   $items = $this->items;

	   // Get the columns registered in the get_columns and get_sortable_columns methods
	   list( $columns, $hidden ) = $this->get_column_info();

	   // Loop for each record
	   if( !empty( $items ) ) { 

	   		foreach( $items as $item ) {

	      		// Open the line
	        	echo '<tr id="record_'.$item['notification_id'].'">';
		      	foreach ( $columns as $column_name => $column_display_name ) {

		         	// Style attributes for each col
		         	$class = "class='$column_name column-$column_name'";
		         	$style = "";

		         	if( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';

		         	$attributes = $class . $style;

		         	// Display the cell
		         	switch ( $column_name ) {
		         		case "cb":						echo '<td '.$attributes.'>'.self::column_cb( $item ).'</td>';												break;
		         		case "notification_content": 	echo '<td '.$attributes.'>'.$item['notification_content'].' <br>'.self::column_notification_content( $item ).'</td>';											break;
		            	case "notification_date": 		echo '<td '.$attributes.'>'.date( 'j, M Y H:i:s', strtotime( $item['notification_date'] ) ).'</td>'; 		break;
		            	case "notification_link": 		echo '<td '.$attributes.'>'.$item['notification_link'].'</td>'; 											break;
		         	}
		      	}

		      	// Close the line
		      	echo'</tr>';

	   		} 

		}

	}

}
