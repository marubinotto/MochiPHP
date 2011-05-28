function deletePost(id, button) {
	if (!window.confirm('Are you sure you want to delete this post?')) 
		return;
	jQuery.get("delete-post", {id: id});
	jQuery(button).closest("div.post").fadeOut("slow");
}
