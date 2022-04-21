<?php
	$config = cmsConfig::getInstance();

	if ($category){ $feed['title'] = $feed['title'].' / '.$category['title']; }
	if ($author){ $feed['title'] = $author['nickname'].' - '.$feed['title']; }
	$feed_title = sprintf(LANG_RSS_FEED_TITLE_FORMAT, $feed['title'], $config->sitename);
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php html($feed_title); ?></title>
		<link><?php html($config->host); ?></link>
		<description><?php html($feed['view'] == 'threads' ? LANG_FORUM_RSS_THREADS : LANG_FORUM_RSS_POSTS); ?></description>
		<language><?php html(cmsCore::getLanguageName()); ?></language>
		<pubDate><?php html(date('r')); ?></pubDate>
		<?php if(!empty($feed['image'])) { ?>
			<image>
				<url><?php echo $config->upload_host_abs.'/'.$feed['image']['normal']; ?></url>
				<title><?php html($feed_title); ?></title>
				<link><?php html($config->host); ?></link>
			</image>
		<?php } ?>
		<atom:link rel="self" type="application/rss+xml" href="<?php html(href_to_current(true)); ?>"/>
		<?php if(!empty($feed['items'])) { ?>
			<?php if ($feed['view'] == 'threads'){?>
				<?php foreach($feed['items'] as $id => $item){ ?>
					<item>
						<?php if(!empty($item['title'])) { ?>
							<title><?php echo htmlspecialchars($item['title']); ?></title>
						<?php } ?>
						<?php if(!empty($item['description'])) { ?>
							<description><?php echo htmlspecialchars($item['description']); ?></description>
						<?php } ?>
						<?php if(!empty($item['user_nickname'])) { ?>
							<author><?php html($item['user_nickname']); ?></author>
						<?php } ?>
						<?php if(!empty($item['pubDate'])) { ?>
							<pubDate><?php html(date('r', strtotime($item[$item['pubDate']]))); ?></pubDate>
						<?php } ?>
						<link><?php echo href_to_abs('forum', $item['slug'] . '.html'); ?></link>
						<guid><?php echo href_to_abs('forum', $item['slug'] . '.html'); ?></guid>
					</item>
				<?php } ?>
			<?php } else {?>
				<?php foreach($feed['items'] as $id => $item){ ?>
					<item>
						<?php if(!empty($item['title'])) { ?>
							<title><?php echo htmlspecialchars($item['title']); ?></title>
						<?php } ?>
						<?php if(!empty($item['content_html'])) { ?>
							<description><?php echo htmlspecialchars($item['content_html']); ?></description>
						<?php } ?>
						<?php if(!empty($item['user_nickname'])) { ?>
							<author><?php html($item['user_nickname']); ?></author>
						<?php } ?>
						<?php if(!empty($item['pubDate'])) { ?>
							<pubDate><?php html(date('r', strtotime($item[$item['pubDate']]))); ?></pubDate>
						<?php } ?>
						<link><?php echo href_to_abs('forum', 'pfind', $item['id']); ?></link>
						<guid><?php echo href_to_abs('forum', 'pfind', $item['id']); ?></guid>
					</item>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	</channel>
</rss>