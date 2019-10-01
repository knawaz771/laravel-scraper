<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte;
use App\Item;

class ScrapController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Scrap Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles scrapping functionality for the application and
    | stores scrapped data in database.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Scrap and store list data into database.
     *
     * @return void
     */
    public function index(Request $request)
    {
    	try {
	        $crawler = Goutte::request('GET', $request->weburl);
		    $crawler->filter('.swiper-slide .carouselHomeCard > a')->each(function ($node) {
		    	$temp = $node->attr('href');
		    	$temp_arr = explode("/items/", $temp); // Extract item title from link

		    	if(!empty($temp_arr[1])){
		    		$temp_item = trim($temp_arr[1]);

		    		$temp_item_arr = explode("-", $temp_item); // Adjust the item title
		    		foreach ($temp_item_arr as $key => $value) {
		    			if($value == 's' || $value == 're'){
		    				$temp_item_arr[$key-1] = $temp_item_arr[$key-1]."'".$value;
		    				unset($temp_item_arr[$key]);
		    			}else{
		    				$temp_item_arr[$key] = ucwords($temp_item_arr[$key]);
		    			}
		    		}

		    		$list_item = implode(" ", $temp_item_arr); // Glue the pieces back

		    		/* Create item record or update if already exists */
		    		Item::updateOrCreate([
	                  'title' => $list_item
	                ],[

	                ]);

		    		print "importing ".$list_item." into databse...";
		    		print "<br>";
		    	}
		    });

		    print "<br>==========================<br>";
		    print "Import Completed";
		    print "<br>==========================<br>";
	    } catch (\Exception $e) {
          return redirect()->route('home')->with('excepMessage', $e->getMessage());
        }
    }
}
