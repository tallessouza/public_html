<?php

namespace App\Services\Contracts;

interface BaseSearchService
{
	public function __construct();

	public function search($keyword);

	public function getTopTitles($keyword);

	public function getKeywords($keyword);

	public function getTopStories($keyword);

	public function getPeopleAlsoAsks($keyword);
}