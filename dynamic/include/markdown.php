<?php

# Set up include pathes needed for markdown:

$path = 'dynamic/markdown/cebe/markdown';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
include 'block/HeadlineTrait.php';
include 'block/HtmlTrait.php';
include 'block/ListTrait.php';
include 'block/QuoteTrait.php';
include 'block/RuleTrait.php';
include 'block/CodeTrait.php';
include 'block/TableTrait.php';
include 'block/FencedCodeTrait.php';
include 'inline/CodeTrait.php';
include 'inline/EmphStrongTrait.php';
include 'inline/LinkTrait.php';
include 'inline/StrikeoutTrait.php';
include 'inline/UrlLinkTrait.php';
include 'Parser.php';
include 'Markdown.php';
include 'GithubMarkdown.php';
//include 'MarkdownExtra.php';
include 'JonglariaMarkdown.php';

# We should probably use classes instead to keep things tidy

function renderMarkdownFile($filename){
	$markdown_file = fopen('www/'.$filename.'/index.md', 'r');
	$markdown = fread($markdown_file,filesize('www/'.$filename.'/index.md'));

	$parser = new \cebe\markdown\JonglariaMarkdown();
	echo $parser->parse($markdown);
}
?>
