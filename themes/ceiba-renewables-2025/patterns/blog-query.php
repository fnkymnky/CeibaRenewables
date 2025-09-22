<?php
/**
 * Title: Blog - Query (12 grid)
 * Slug: ceiba/blog-query
 * Categories: query, posts
 * Block Types: core/query
  * Inserter: true
 * Viewport Width: 1280
 */
?>
<!-- wp:group {"tagName":"main","className":"blog-index","layout":{"type":"constrained"}} -->
<main class="wp-block-group blog-index">
	<!-- wp:query {"className":"posts-grid","query":{"perPage":12,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false}} -->
		<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
			<!-- wp:pattern {"slug":"ceiba/blog-item"} /-->
		<!-- /wp:post-template -->

		<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"},"className":"posts-pagination"} -->
			<!-- wp:query-pagination-previous {"label":"Newer"} /-->
			<!-- wp:query-pagination-numbers /-->
			<!-- wp:query-pagination-next {"label":"Older"} /-->
		<!-- /wp:query-pagination -->
	<!-- /wp:query -->
</main>
<!-- /wp:group -->