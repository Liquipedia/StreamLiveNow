<?php

public function LogError($ErrorMessage){
	error_log($ErrorMessage, 0);
}