<?php
if (!$json) {
	print json_encode(array('noData'=>true));
} else {
	print $json;
}