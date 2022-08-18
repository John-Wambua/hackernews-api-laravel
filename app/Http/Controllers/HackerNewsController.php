<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class HackerNewsController extends Controller
{
    public function last_twenty_five(): JsonResponse
    {
        $responses = $this->fetch_news(25);
        $top_words = $this->get_top_ten_words($responses);
        return response()->json([
            'status'=>'success',
            'words'=>$top_words
        ], Response::HTTP_OK);
    }

    public function last_week_words(): JsonResponse
    {
        $responses = $this->fetch_news(100);
        $last_week_stories = [];
        foreach ($responses as $response){
            $time= $response->json()['time'];
            $one_week_ago = strtotime('-1 week');
            if ($time > $one_week_ago){
                //sooner than 1 week ago
                $last_week_stories[]=$response;
            }
        }
        $top_words = $this->get_top_ten_words($last_week_stories);
        return response()->json([
            'status'=>'success',
            'words'=>$top_words
        ], Response::HTTP_OK);
    }

    public function last_six_hundred_stories(): JsonResponse
    {
        $responses = $this->fetch_news(600);
        $top_karma_responses = [];
        foreach ($responses as $response) {
            $user_id = $response->json()['by'];
            $user = Http::get(
                'https://hacker-news.firebaseio.com/v0/user/'.$user_id.'.json?print=pretty'
            );
            if ($user->json()['karma'] >=10000){
                $top_karma_responses[] = $response;
            }
        }
        $top_words = $this->get_top_ten_words($top_karma_responses);
        return response()->json([
            'status'=>'success',
            'words'=>$top_words
        ], Response::HTTP_OK);
    }

    public function get_top_ten_words(array $array): array
    {
        $titles = [];
        foreach ($array as $response){
            $titles[]= $response->json()['title'];
        }
        $words = [];
        foreach($titles as $title) {
            $words=  array_merge($words,explode(" ", $title));
        }
        $word_count=array_keys(array_count_values(array_map("strtolower", $words)));
        arsort($word_count);
        return array_slice( $word_count, 0, 10);
    }

    public function fetch_news(int $limit): array
    {
        $response = Http::get(
            'https://hacker-news.firebaseio.com/v0/topstories.json?orderBy=%22$key%22&limitToFirst='.$limit
        );
        $story_ids = $response->json();
        return Http::pool(function (Pool $pool) use ($story_ids) {
            $responses = [];
            foreach ($story_ids as $story_id){
                $response = $pool->get('https://hacker-news.firebaseio.com/v0/item/'.$story_id.'.json');
                $responses[]=$response;
            }
            return $responses;
        });
    }
}
