<?php
require_once ('admin.php');

$wpvarstoreset = array ('action', 'cat_id', 'linkurl', 'name', 'image', 'description', 'visible', 'target', 'category', 'link_id', 'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel', 'notes', 'linkcheck[]');

for ($i = 0; $i < count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset ($$wpvar)) {
		if (empty ($_POST["$wpvar"])) {
			if (empty ($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

if ('' != $_POST['deletebookmarks'])
	$action = 'deletebookmarks';
if ('' != $_POST['move'])
	$action = 'move';
if ('' != $_POST['linkcheck'])
	$linkcheck = $_POST[linkcheck];

$this_file = 'link-manager.php';

switch ($action) {
		case 'deletebookmarks' :
		check_admin_referer();

		// check the current user's level first.
		if (!current_user_can('manage_links'))
			die(__("Cheatin' uh ?"));

		//for each link id (in $linkcheck[]) change category to selected value
		if (count($linkcheck) == 0) {
			header('Location: '.$this_file);
			exit;
		}

		$deleted = 0;
		foreach ($linkcheck as $link_id) {
			$link_id = (int) $link_id;
			
			if ( wp_delete_link($link_id) )
				$deleted++;
		}

		header("Location: $this_file?deleted=$deleted");
		break;

	case 'move' :
		check_admin_referer();

		// check the current user's level first.
		if (!current_user_can('manage_links'))
			die(__("Cheatin' uh ?"));

		//for each link id (in $linkcheck[]) change category to selected value
		if (count($linkcheck) == 0) {
			header('Location: '.$this_file);
			exit;
		}
		$all_links = join(',', $linkcheck);
		// should now have an array of links we can change
		//$q = $wpdb->query("update $wpdb->links SET link_category='$category' WHERE link_id IN ($all_links)");

		header('Location: '.$this_file);
		break;

	case 'add' :
		check_admin_referer();

		add_link();

		header('Location: '.$_SERVER['HTTP_REFERER'].'?added=true');
		break;

	case 'save' :
		check_admin_referer();

		$link_id = (int) $_POST['link_id'];
		edit_link($link_id);

		wp_redirect($this_file);
		exit;
		break;

	case 'delete' :
		check_admin_referer();

		if (!current_user_can('manage_links'))
			die(__("Cheatin' uh ?"));

		$link_id = (int) $_GET['link_id'];

		wp_delete_link($link_id);

		wp_redirect($this_file);
		break;

	case 'edit' :
		$xfn_js = true;
		$editing = true;
		$parent_file = 'link-manager.php';
		$submenu_file = 'link-manager.php';
		$title = __('Edit Bookmark');
		include_once ('admin-header.php');
		if (!current_user_can('manage_links'))
			die(__('You do not have sufficient permissions to edit the bookmarks for this blog.'));

		$link_id = (int) $_GET['link_id'];

		if (!$link = get_link_to_edit($link_id))
			die(__('Link not found.'));

		include ('edit-link-form.php');
		break;

	default :
		break;
}

include ('admin-footer.php');
?>