<?php

$all_items = array();
//Офисная недвижимость
$ofice_count = 0;	
$ofice = array(2);
//Торговая
$torg_count = 0;	
$torg = array(1);
//Земельные участки
$zemuch_count = 0;	
$zemuch = array(3);
//Производство
$pr_count = 0;	
$pr = array(4);
//аренда
$rent_count = 0;
//массив метро	
$metro_colors_b61d8e = array(56,60,64,76,78,80,89,96,109,111,112,113,119,412,413,414);
$metro_colors_ef1e25 = array(32,36,55,57,58,62,63,65,66,69,71,77,83,84,85,92,93,103,107,114,118,162,164,327,330,333,336);
$metro_colors_029a55 = array(54,61,68,70,74,79,87,88,97,104,105,108,191,320,390,408);
$metro_colors_fbaa33 = array(59,72,73,82,86,95,166,176,306,407,410);
$metro_colors_019ee0 = array(4,48,67,75,81,90,91,94,98,99,100,101,102,106,110,115,116,117,120,184,192,246,250,254,338,382,383,385,409);
//для пагинации
$pages_count;
$one_page_count = 21;	
//запрос на коммерцию
/*$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,'https://lc.yucrm.ru/api/v1/e4726f6cffa61279f39d74dd9fee9f4d/realty.objects.commerce/list');
curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl, CURLOPT_REFERER, "$url_site");
curl_setopt($curl, CURLOPT_POST,true);
curl_setopt($curl, CURLOPT_POSTFIELDS,"page=1&order_by=created_at&order_dir=asc&mode=full&limit=1000");

$id = $modx->documentIdentifier;
echo $id;
*/
$out = file_get_contents('data.txt');
$list = json_decode($out,true);
$all_items = $list['result']['list'];	
//print_r($list['result']['list']);	
//Код для фильтров
$none_filter = 'active';//выводим класс active для текущего вида фильтрации
$name_sort = 'По умолчанию';
if(isset($_GET['filts']) && $_GET['filts'] == 'price_desc'){
$price_desc = 'active';
$none_filter = '';	
$name_sort = 'По цене - убывание';	
}	
if(isset($_GET['filts']) && $_GET['filts'] == 'price_asc'){
$price_asc = 'active';
$none_filter = '';	
$name_sort = 'По цене - возрастание';	
}	
if(isset($_GET['filts']) && $_GET['filts'] == 'name_asc'){
$name_asc = 'active';
$none_filter = '';
$name_sort = 'По названию А-Я';	
}
if(isset($_GET['filts']) && $_GET['filts'] == 'name_desc'){
$name_desc = 'active';
$none_filter = '';
$name_sort = 'По названию Я-А';		
}
$filtered_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];//Формируем верный get массив из которого будут хвататьпараметры наши сортировщики
if(stripos($filtered_url, '?filts=' )) {
$str_mark = '?';
	
}
else{
	if(stripos( $filtered_url,'?')){
		$str_mark = '&';

	}
	else{$str_mark = '?';};
	
}
if(isset($_GET['filts'])){
	$filtered_url = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],$str_mark.'filts='));
}

//Сама сортировка
//цена
// По возрастанию:
if($_GET['filts'] == 'price_asc'){
	function cmp_function($a, $b){
		return ($a['price'] > $b['price']);
	} 
	uasort($all_items, 'cmp_function');
}
if($_GET['filts'] == 'price_desc'){
// По убыванию:
	function cmp_function_desc($a, $b){
		return ($a['price'] < $b['price']);
	}
	uasort($all_items, 'cmp_function_desc');
}
//алфавит
//возрастание
if($_GET['filts'] == 'name_asc'){
	function alf_cmp_function($a, $b){
		return ($a['title'] > $b['title']);
	} 
	uasort($all_items, 'alf_cmp_function');
}
if($_GET['filts'] == 'name_desc'){
	function alf_cmp_function_ub($a, $b){
		return ($a['title'] < $b['title']);
	} 
	uasort($all_items, 'alf_cmp_function_ub');
}

$modx->setPlaceholder('none_filter',$none_filter);
$modx->setPlaceholder('price_asc',$price_asc);
$modx->setPlaceholder('price_desc',$price_desc);
$modx->setPlaceholder('name_asc',$name_asc);
$modx->setPlaceholder('name_desc',$name_desc);
$modx->setPlaceholder('name_sort',$name_sort);
$modx->setPlaceholder('filtered_url',$filtered_url);
$modx->setPlaceholder('str_mark',$str_mark);



//считаем количество коммерции на продажу
foreach ( $all_items as $item ) {
	if($item['is_rent'] == false  &&  $item['is_hidden_base'] == false){
		$items_count_sale += 1;
	}
	if($item['is_rent'] == true  &&  $item['is_hidden_base'] == false){
		$items_count_rent += 1;
	}	
	if($item['is_rent'] == false && in_array($item['site_category']['id'],$ofice)  &&  $item['is_hidden_base'] == false){
		$items_count_ofice += 1;
	}
	if($item['is_rent'] == false && in_array($item['site_category']['id'],$torg)  &&  $item['is_hidden_base'] == false){
		$items_count_torg += 1;
	}
	if($item['is_rent'] == false && in_array($item['site_category']['id'],$zemuch)  &&  $item['is_hidden_base'] == false){
		$items_count_uch += 1;
	}
	if($item['is_rent'] == false && in_array($item['site_category']['id'],$pr)  &&  $item['is_hidden_base'] == false){
		$items_count_pr += 1;
	}
}
	
if($modx->documentIdentifier == 20){
	$items_count = $items_count_sale;
}
if($modx->documentIdentifier == 23){
	$items_count = $items_count_torg;
}
if($modx->documentIdentifier == 24){
	$items_count = $items_count_ofice;

}
if($modx->documentIdentifier == 25){
	$items_count = $items_count_uch;

}
if($modx->documentIdentifier == 26){
	$items_count = $items_count_pr;

}
if($modx->documentIdentifier == 21){
	$items_count = $items_count_rent;
	
}

$counter_br = 1;
$counter_sr = 0;

$counter_br_rent = 1;
$counter_sr_rent = 0;

$counter_br_torg = 1;
$counter_sr_torg = 0;

$counter_br_ofice = 1;
$counter_sr_ofice = 0;

$counter_br_uch = 1;
$counter_sr_uch = 0;

$counter_br_pr = 1;
$counter_sr_pr = 0;


include MODX_BASE_PATH.'assets/snippets/load_flats/paginator.php';

foreach ( $all_items as $item ) {
	//объекты для блока "акции"
	if($item['has_action'] == true  &&  $item['is_hidden_base'] == false ){
		
		if($item['photos'][0] != null){
			$photo_sale_main = $item['photos'][0];	

		}
		else {
			if($item['layouts'][0] != null){
				$photo_sale_main = $item['layouts'][0];	

			}
			else{
				$photo_sale_main = 'assets/templates/lider/img/no_photo.jpg';
			}	

		}
	//шаблон объекта акция для главной	
	$out_action .= '
<form  method="GET" action="'.$modx->makeUrl(35).'" name="send_query'.$item['id'].'" class="shop2_product_item shop2-product-item ">
	<input type="hidden" name="id_obj" value="'.$item['id'].'">
	<div class="new_labels price">
		<div class="product-flag label_item rent" style="background: #fc906c;">
			Продажа'.$item['address']['station_distance']['id'].'
			<span style="border-bottom: 10px solid #fc906c; border-right: 6px solid transparent;"></span>
		</div>
	</div>
	<div class="shop2_product_inner">
		<div class="product_top_wr">
			<div class="product-top">
				<div class="product-image">
					<a href="javascript:document.send_query'.$item['id'].'.submit()" >
						<img class="lazyload" src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" data-src="'.$photo_sale_main.'" alt="'.$item['title'].'" title="'.$item['title'].'" />
					</a>
				</div>
				<div class="new_labels">
					<div class="product-flag label_item rent" style="background: #fc906c;">
						Продажа
						<span style=" border-right: 6px solid transparent; border-top: 10px solid #fc906c;"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="product_bot_wr">
			<div class="product_bot_in1">
				<div class="product_in1_left">
					<div class="product-name"><a href="javascript:document.send_query'.$item['id'].'.submit()">'.$item['title'].'</a></div>
					<div class="prod_address">
						'.$item['address']['full_address'].'
					</div>
					<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$out_action .= '

							<li data-kinds="" data-name="metro_station" data-value="">';
			
			
			$check = true;
			if(in_array($item['address']['station']['id'], $metro_colors_b61d8e)){
				$out_action .= '<span class="metro_icon" style="background-color: #b61d8e"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_ef1e25)){
				$out_action .= '<span class="metro_icon" style="background-color: #ef1e25"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_029a55)){
				$out_action .= '<span class="metro_icon" style="background-color: #029a55"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_fbaa33)){
				$out_action .= '<span class="metro_icon" style="background-color: #fbaa33"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_019ee0)){
				$out_action .= '<span class="metro_icon" style="background-color: #019ee0"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}
			
			if($check){
				$out_action .= '<span class="metro_title">'.$item['address']['station']['name'];
			}
			if(isset($item['address']['station_distance']['id'])){
				$out_action .= ' - '.$item['address']['station_distance']['name'].'</span></li>';
			}
			else{
				'</span></li>';
			}
		}
		$out_action .= '
					</ul>
				</div>
				<div class="product_in1_right">
					<div class="product-label">
						<div class="product-spec">Акция</div>
					</div>
					<div class="product-price">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit">'.$item['price_unit']['name'].'</span>                   
					</div>';
					if($item['old_price'] != 0){	
				$out_action .= '	<div class="price-old "><span>
						<strong>'.number_format($item['old_price'], 0, '', ' ').'</strong>
					<em class="fa fa-rouble"></em>
					</span>
					</div>';
				}
				$out_action .= '</div>
			</div>
			<div class="product_bot_in2">
				<div class="product_in2_wrap">
					<div class="pr_list_options">
						<div class="prod_address">
							'.$item['address']['full_address'].'
						</div>
						<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$out_action .= '			
							<li data-kinds="1511470016" data-name="metro_station" data-value="75c7c0d73bc89e692a849cddac7d0fb7"><span class="metro_icon" style="background-color: #cc0000"></span><span class="metro_title">'.$item['address']['station']['name'].'</span></li>';
		}	
		$out_action .= '
						</ul>
					</div>
					<div class="options_title_wr">
						<div class="options_show_btn">
							<div class="show_btn">Параметры</div>
						</div>
					</div>
					<div class="all_options_wrap">
					</div>
				</div>
				<div class="product-bot">
					<div class="product_pricelist">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit"></span>                            	
					</div>
					<a href="javascript:document.send_query'.$item['id'].'.submit()" class="product_link">
						<span>Подробнее</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>';
	
	}
	
	//Продажа производства
	if($item['is_rent'] == false && in_array($item['site_category']['id'],$pr)  &&  $item['is_hidden_base'] == false){
		
	
		if($item['photos'][0] != null){
			$photo_sale_main = $item['photos'][0];	

		}
		else {
			if($item['layouts'][0] != null){
				$photo_sale_main = $item['layouts'][0];	

			}
			else{
				$photo_sale_main = 'assets/templates/lider/img/no_photo.jpg';
			}	

		}
	//шаблон объекта продажи  для страницы "продажа"	
	$sail_pr_price[] = $item['price']; 	
	$sail_pr .= '
<form  method="GET" action="'.$modx->makeUrl(35).'" name="send_query'.$item['id'].'" class="shop2_product_item shop2-product-item ">
	<input type="hidden" name="id_obj" value="'.$item['id'].'">
	<div class="new_labels price">
		<div class="product-flag label_item rent" style="background: #fc906c;">
			Продажа'.$item['address']['station_distance']['id'].'
			<span style="border-bottom: 10px solid #fc906c; border-right: 6px solid transparent;"></span>
		</div>
	</div>
	<div class="shop2_product_inner">
		<div class="product_top_wr">
			<div class="product-top">
				<div class="product-image">
					<a href="javascript:document.send_query'.$item['id'].'.submit()" >
						<img class="lazyload" src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" data-src="'.$photo_sale_main.'" alt="'.$item['title'].'" title="'.$item['title'].'" />
					</a>
				</div>
				<div class="new_labels">
					<div class="product-flag label_item rent" style="background: #fc906c;">
						Продажа
						<span style=" border-right: 6px solid transparent; border-top: 10px solid #fc906c;"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="product_bot_wr">
			<div class="product_bot_in1">
				<div class="product_in1_left">
					<div class="product-name"><a href="javascript:document.send_query'.$item['id'].'.submit()">'.$item['title'].'</a></div>
					<div class="prod_address">
						'.$item['address']['full_address'].'
					</div>
					<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_pr .= '

							<li data-kinds="" data-name="metro_station" data-value="">';
			
			
			$check = true;
			if(in_array($item['address']['station']['id'], $metro_colors_b61d8e)){
				$sail_pr .= '<span class="metro_icon" style="background-color: #b61d8e"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_ef1e25)){
				$sail_pr .= '<span class="metro_icon" style="background-color: #ef1e25"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_029a55)){
				$sail_pr .= '<span class="metro_icon" style="background-color: #029a55"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_fbaa33)){
				$sail_pr .= '<span class="metro_icon" style="background-color: #fbaa33"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_019ee0)){
				$sail_pr .= '<span class="metro_icon" style="background-color: #019ee0"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}
			
			if($check){
				$sail_pr .= '<span class="metro_title">'.$item['address']['station']['name'];
			}
			if(isset($item['address']['station_distance']['id'])){
				$sail_pr .= ' - '.$item['address']['station_distance']['name'].'</span></li>';
			}
			else{
				'</span></li>';
			}
		}
		$sail_pr .= '
					</ul>
				</div>
				<div class="product_in1_right">
					<div class="product-price">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit">'.$item['price_unit']['name'].'</span>                            	
					</div>
				</div>
			</div>
			<div class="product_bot_in2">
				<div class="product_in2_wrap">
					<div class="pr_list_options">
						<div class="prod_address">
							'.$item['address']['full_address'].'
						</div>
						<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_pr .= '			
							<li data-kinds="1511470016" data-name="metro_station" data-value="75c7c0d73bc89e692a849cddac7d0fb7"><span class="metro_icon" style="background-color: #cc0000"></span><span class="metro_title">'.$item['address']['station']['name'].'</span></li>';
		}	
		$sail_pr .= '
						</ul>
					</div>
					<div class="options_title_wr">
						<div class="options_show_btn">
							<div class="show_btn">Параметры</div>
						</div>
					</div>
					<div class="all_options_wrap">
					</div>
				</div>
				<div class="product-bot">
					<div class="product_pricelist">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit"></span>                            	
					</div>
					<a href="javascript:document.send_query'.$item['id'].'.submit()" class="product_link">
						<span>Подробнее</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>';
	
	}
	//Продажа участки
	if($item['is_rent'] == false && in_array($item['site_category']['id'],$zemuch)  &&  $item['is_hidden_base'] == false){
		

		if($item['photos'][0] != null){
			$photo_sale_main = $item['photos'][0];	

		}
		else {
			if($item['layouts'][0] != null){
				$photo_sale_main = $item['layouts'][0];	

			}
			else{
				$photo_sale_main = 'assets/templates/lider/img/no_photo.jpg';
			}	

		}
	//шаблон объекта продажи  для страницы "продажа"
	$sail_zemuch_price[] = $item['price']; 	
	$sail_zemuch .= '
<form  method="GET" action="'.$modx->makeUrl(35).'" name="send_query'.$item['id'].'" class="shop2_product_item shop2-product-item ">
	<input type="hidden" name="id_obj" value="'.$item['id'].'">
	<div class="new_labels price">
		<div class="product-flag label_item rent" style="background: #fc906c;">
			Продажа'.$item['address']['station_distance']['id'].'
			<span style="border-bottom: 10px solid #fc906c; border-right: 6px solid transparent;"></span>
		</div>
	</div>
	<div class="shop2_product_inner">
		<div class="product_top_wr">
			<div class="product-top">
				<div class="product-image">
					<a href="javascript:document.send_query'.$item['id'].'.submit()" >
						<img class="lazyload" src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" data-src="'.$photo_sale_main.'" alt="'.$item['title'].'" title="'.$item['title'].'" />
					</a>
				</div>
				<div class="new_labels">
					<div class="product-flag label_item rent" style="background: #fc906c;">
						Продажа
						<span style=" border-right: 6px solid transparent; border-top: 10px solid #fc906c;"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="product_bot_wr">
			<div class="product_bot_in1">
				<div class="product_in1_left">
					<div class="product-name"><a href="javascript:document.send_query'.$item['id'].'.submit()">'.$item['title'].'</a></div>
					<div class="prod_address">
						'.$item['address']['full_address'].'
					</div>
					<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_zemuch .= '

							<li data-kinds="" data-name="metro_station" data-value="">';
			
			
			$check = true;
			if(in_array($item['address']['station']['id'], $metro_colors_b61d8e)){
				$sail_zemuch .= '<span class="metro_icon" style="background-color: #b61d8e"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_ef1e25)){
				$sail_zemuch .= '<span class="metro_icon" style="background-color: #ef1e25"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_029a55)){
				$sail_zemuch .= '<span class="metro_icon" style="background-color: #029a55"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_fbaa33)){
				$sail_zemuch .= '<span class="metro_icon" style="background-color: #fbaa33"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_019ee0)){
				$sail_zemuch .= '<span class="metro_icon" style="background-color: #019ee0"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}
			
			if($check){
				$sail_zemuch .= '<span class="metro_title">'.$item['address']['station']['name'];
			}
			if(isset($item['address']['station_distance']['id'])){
				$sail_zemuch .= ' - '.$item['address']['station_distance']['name'].'</span></li>';
			}
			else{
				'</span></li>';
			}
		}
		$sail_zemuch .= '
					</ul>
				</div>
				<div class="product_in1_right">
					<div class="product-price">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit">'.$item['price_unit']['name'].'</span>                            	
					</div>
				</div>
			</div>
			<div class="product_bot_in2">
				<div class="product_in2_wrap">
					<div class="pr_list_options">
						<div class="prod_address">
							'.$item['address']['full_address'].'
						</div>
						<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_zemuch .= '			
							<li data-kinds="1511470016" data-name="metro_station" data-value="75c7c0d73bc89e692a849cddac7d0fb7"><span class="metro_icon" style="background-color: #cc0000"></span><span class="metro_title">'.$item['address']['station']['name'].'</span></li>';
		}	
		$sail_zemuch .= '
						</ul>
					</div>
					<div class="options_title_wr">
						<div class="options_show_btn">
							<div class="show_btn">Параметры</div>
						</div>
					</div>
					<div class="all_options_wrap">
					</div>
				</div>
				<div class="product-bot">
					<div class="product_pricelist">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit"></span>                            	
					</div>
					<a href="javascript:document.send_query'.$item['id'].'.submit()" class="product_link">
						<span>Подробнее</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>';
	
	}
	//Продажа торговая недвижимость
	//продажа торговая
	if($item['is_rent'] == false && in_array($item['site_category']['id'],$torg)  &&  $item['is_hidden_base'] == false){
		
		if($item['photos'][0] != null){
			$photo_sale_main = $item['photos'][0];	

		}
		else {
			if($item['layouts'][0] != null){
				$photo_sale_main = $item['layouts'][0];	

			}
			else{
				$photo_sale_main = 'assets/templates/lider/img/no_photo.jpg';
			}	

		}
	//шаблон объекта продажи  для страницы "продажа"
	$sail_torg_price[] = $item['price']; 	
	$sail_torg .= '
<form  method="GET" action="'.$modx->makeUrl(35).'" name="send_query'.$item['id'].'" class="shop2_product_item shop2-product-item ">
	<input type="hidden" name="id_obj" value="'.$item['id'].'">
	<div class="new_labels price">
		<div class="product-flag label_item rent" style="background: #fc906c;">
			Продажа'.$item['address']['station_distance']['id'].'
			<span style="border-bottom: 10px solid #fc906c; border-right: 6px solid transparent;"></span>
		</div>
	</div>
	<div class="shop2_product_inner">
		<div class="product_top_wr">
			<div class="product-top">
				<div class="product-image">
					<a href="javascript:document.send_query'.$item['id'].'.submit()" >
						<img class="lazyload" src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" data-src="'.$photo_sale_main.'" alt="'.$item['title'].'" title="'.$item['title'].'" />
					</a>
				</div>
				<div class="new_labels">
					<div class="product-flag label_item rent" style="background: #fc906c;">
						Продажа
						<span style=" border-right: 6px solid transparent; border-top: 10px solid #fc906c;"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="product_bot_wr">
			<div class="product_bot_in1">
				<div class="product_in1_left">
					<div class="product-name"><a href="javascript:document.send_query'.$item['id'].'.submit()">'.$item['title'].'</a></div>
					<div class="prod_address">
						'.$item['address']['full_address'].'
					</div>
					<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_torg .= '

							<li data-kinds="" data-name="metro_station" data-value="">';
			
			
			$check = true;
			if(in_array($item['address']['station']['id'], $metro_colors_b61d8e)){
				$sail_torg .= '<span class="metro_icon" style="background-color: #b61d8e"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_ef1e25)){
				$sail_torg .= '<span class="metro_icon" style="background-color: #ef1e25"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_029a55)){
				$sail_torg .= '<span class="metro_icon" style="background-color: #029a55"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_fbaa33)){
				$sail_torg .= '<span class="metro_icon" style="background-color: #fbaa33"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_019ee0)){
				$sail_torg .= '<span class="metro_icon" style="background-color: #019ee0"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}
			
			if($check){
				$sail_torg .= '<span class="metro_title">'.$item['address']['station']['name'];
			}
			if(isset($item['address']['station_distance']['id'])){
				$sail_torg .= ' - '.$item['address']['station_distance']['name'].'</span></li>';
			}
			else{
				'</span></li>';
			}
		}
		$sail_torg .= '
					</ul>
				</div>
				<div class="product_in1_right">
					<div class="product-price">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit">'.$item['price_unit']['name'].'</span>                            	
					</div>
				</div>
			</div>
			<div class="product_bot_in2">
				<div class="product_in2_wrap">
					<div class="pr_list_options">
						<div class="prod_address">
							'.$item['address']['full_address'].'
						</div>
						<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_torg .= '			
							<li data-kinds="1511470016" data-name="metro_station" data-value="75c7c0d73bc89e692a849cddac7d0fb7"><span class="metro_icon" style="background-color: #cc0000"></span><span class="metro_title">'.$item['address']['station']['name'].'</span></li>';
		}	
		$sail_torg .= '
						</ul>
					</div>
					<div class="options_title_wr">
						<div class="options_show_btn">
							<div class="show_btn">Параметры</div>
						</div>
					</div>
					<div class="all_options_wrap">
					</div>
				</div>
				<div class="product-bot">
					<div class="product_pricelist">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit"></span>                            	
					</div>
					<a href="javascript:document.send_query'.$item['id'].'.submit()" class="product_link">
						<span>Подробнее</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>';
	
	}
	//Продажа офисная недвижимость
	//продажа офисная
	if($item['is_rent'] == false && in_array($item['site_category']['id'],$ofice)  &&  $item['is_hidden_base'] == false){
		
		if($item['photos'][0] != null){
			$photo_sale_main = $item['photos'][0];	

		}
		else {
			if($item['layouts'][0] != null){
				$photo_sale_main = $item['layouts'][0];	

			}
			else{
				$photo_sale_main = 'assets/templates/lider/img/no_photo.jpg';
			}	

		}
	//шаблон объекта продажи  для страницы "продажа"	
	$sail_ofice_price[] = $item['price']; 	
	$sail_ofice .= '
<form  method="GET" action="'.$modx->makeUrl(35).'" name="send_query'.$item['id'].'" class="shop2_product_item shop2-product-item ">
	<input type="hidden" name="id_obj" value="'.$item['id'].'">
	<div class="new_labels price">
		<div class="product-flag label_item rent" style="background: #fc906c;">
			Продажа'.$item['address']['station_distance']['id'].'
			<span style="border-bottom: 10px solid #fc906c; border-right: 6px solid transparent;"></span>
		</div>
	</div>
	<div class="shop2_product_inner">
		<div class="product_top_wr">
			<div class="product-top">
				<div class="product-image">
					<a href="javascript:document.send_query'.$item['id'].'.submit()" >
						<img class="lazyload" src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" data-src="'.$photo_sale_main.'" alt="'.$item['title'].'" title="'.$item['title'].'" />
					</a>
				</div>
				<div class="new_labels">
					<div class="product-flag label_item rent" style="background: #fc906c;">
						Продажа
						<span style=" border-right: 6px solid transparent; border-top: 10px solid #fc906c;"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="product_bot_wr">
			<div class="product_bot_in1">
				<div class="product_in1_left">
					<div class="product-name"><a href="javascript:document.send_query'.$item['id'].'.submit()">'.$item['title'].'</a></div>
					<div class="prod_address">
						'.$item['address']['full_address'].'
					</div>
					<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_ofice .= '

							<li data-kinds="" data-name="metro_station" data-value="">';
			
			
			$check = true;
			if(in_array($item['address']['station']['id'], $metro_colors_b61d8e)){
				$sail_ofice .= '<span class="metro_icon" style="background-color: #b61d8e"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_ef1e25)){
				$sail_ofice .= '<span class="metro_icon" style="background-color: #ef1e25"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_029a55)){
				$sail_ofice .= '<span class="metro_icon" style="background-color: #029a55"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_fbaa33)){
				$sail_ofice .= '<span class="metro_icon" style="background-color: #fbaa33"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_019ee0)){
				$sail_ofice .= '<span class="metro_icon" style="background-color: #019ee0"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}
			
			if($check){
				$sail_ofice .= '<span class="metro_title">'.$item['address']['station']['name'];
			}
			if(isset($item['address']['station_distance']['id'])){
				$sail_ofice .= ' - '.$item['address']['station_distance']['name'].'</span></li>';
			}
			else{
				'</span></li>';
			}
		}
		$sail_ofice .= '
					</ul>
				</div>
				<div class="product_in1_right">
					<div class="product-price">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit">'.$item['price_unit']['name'].'</span>                            	
					</div>
				</div>
			</div>
			<div class="product_bot_in2">
				<div class="product_in2_wrap">
					<div class="pr_list_options">
						<div class="prod_address">
							'.$item['address']['full_address'].'
						</div>
						<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_ofice .= '			
							<li data-kinds="1511470016" data-name="metro_station" data-value="75c7c0d73bc89e692a849cddac7d0fb7"><span class="metro_icon" style="background-color: #cc0000"></span><span class="metro_title">'.$item['address']['station']['name'].'</span></li>';
		}	
		$sail_ofice .= '
						</ul>
					</div>
					<div class="options_title_wr">
						<div class="options_show_btn">
							<div class="show_btn">Параметры</div>
						</div>
					</div>
					<div class="all_options_wrap">
					</div>
				</div>
				<div class="product-bot">
					<div class="product_pricelist">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit"></span>                            	
					</div>
					<a href="javascript:document.send_query'.$item['id'].'.submit()" class="product_link">
						<span>Подробнее</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>';
	
	}
	//продажа коммерция
	/*$pages_count = 8;
$one_page_count = 21;$before_offset = ($page_here - 1) * 21;
$after_offset = ($pages_count - $page_here) * 21;		$counter_br = 0;
		$counter_sr = 0;
		$counter_a = 1;*/
	if($item['is_rent'] == false  &&  $item['is_hidden_base'] == false){
		
		if($item['photos'][0] != null){
			$photo_sale_main = $item['photos'][0];	

		}
		else {
			if($item['layouts'][0] != null){
				$photo_sale_main = $item['layouts'][0];	

			}
			else{
				$photo_sale_main = 'assets/templates/lider/img/no_photo.jpg';
			}	

		}
		
	//шаблон объекта продажи  для страницы "продажа"
	$sail_objects_price[] = $item['price']; 	
	$sail_objects .= '
<form  method="GET" action="'.$modx->makeUrl(35).'" name="send_query'.$item['id'].'" class="shop2_product_item shop2-product-item ">
	<input type="hidden" name="id_obj" value="'.$item['id'].'">
	<div class="new_labels price">
		<div class="product-flag label_item rent" style="background: #fc906c;">
			Продажа'.$item['address']['station_distance']['id'].'
			<span style="border-bottom: 10px solid #fc906c; border-right: 6px solid transparent;"></span>
		</div>
	</div>
	<div class="shop2_product_inner">
		<div class="product_top_wr">
			<div class="product-top">
				<div class="product-image">
					<a href="javascript:document.send_query'.$item['id'].'.submit()" >
						<img class="lazyload"  src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" data-src="'.$photo_sale_main.'" alt="'.$item['title'].'" title="'.$item['title'].'" />
					</a>
				</div>
				<div class="new_labels">
					<div class="product-flag label_item rent" style="background: #fc906c;">
						Продажа
						<span style=" border-right: 6px solid transparent; border-top: 10px solid #fc906c;"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="product_bot_wr">
			<div class="product_bot_in1">
				<div class="product_in1_left">
					<div class="product-name"><a href="javascript:document.send_query'.$item['id'].'.submit()">'.$item['title'].'</a></div>
					<div class="prod_address">
						'.$item['address']['full_address'].'
					</div>
					<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_objects .= '

							<li data-kinds="" data-name="metro_station" data-value="">';
			
			
			$check = true;
			if(in_array($item['address']['station']['id'], $metro_colors_b61d8e)){
				$sail_objects .= '<span class="metro_icon" style="background-color: #b61d8e"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_ef1e25)){
				$sail_objects .= '<span class="metro_icon" style="background-color: #ef1e25"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_029a55)){
				$sail_objects .= '<span class="metro_icon" style="background-color: #029a55"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_fbaa33)){
				$sail_objects .= '<span class="metro_icon" style="background-color: #fbaa33"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_019ee0)){
				$sail_objects .= '<span class="metro_icon" style="background-color: #019ee0"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}
			
			if($check){
				$sail_objects .= '<span class="metro_title">'.$item['address']['station']['name'];
			}
			if(isset($item['address']['station_distance']['id'])){
				$sail_objects .= ' - '.$item['address']['station_distance']['name'].'</span></li>';
			}
			else{
				'</span></li>';
			}
		}
		$sail_objects .= '
					</ul>
				</div>
				<div class="product_in1_right">
					<div class="product-price">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit">'.$item['price_unit']['name'].'</span>                            	
					</div>
				</div>
			</div>
			<div class="product_bot_in2">
				<div class="product_in2_wrap">
					<div class="pr_list_options">
						<div class="prod_address">
							'.$item['address']['full_address'].'
						</div>
						<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$sail_objects .= '			
							<li data-kinds="1511470016" data-name="metro_station" data-value="75c7c0d73bc89e692a849cddac7d0fb7"><span class="metro_icon" style="background-color: #cc0000"></span><span class="metro_title">'.$item['address']['station']['name'].'</span></li>';
		}	
		$sail_objects .= '
						</ul>
					</div>
					<div class="options_title_wr">
						<div class="options_show_btn">
							<div class="show_btn">Параметры</div>
						</div>
					</div>
					<div class="all_options_wrap">
					</div>
				</div>
				<div class="product-bot">
					<div class="product_pricelist">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit"></span>                            	
					</div>
					<a href="javascript:document.send_query'.$item['id'].'.submit()" class="product_link">
						<span>Подробнее</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>';
	
	}
	//аренда коммерция
	if($item['is_rent'] == true  &&  $item['is_hidden_base'] == false){

	$rent_count += 1;
	if($item['photos'][0] != null){
		$photo_rent_main = $item['photos'][0];	

	}
	else {
		if($item['layouts'][0] != null){
			$photo_rent_main = $item['layouts'][0];	

		}
		else{
			$photo_rent_main = 'assets/templates/lider/img/no_photo.jpg';
		}	

	}
	//шаблон объекта аренды на главной и для страницы "аренда"
	$rent_on_main_price[] = $item['price']; 		
	$rent_on_main .= '
<form  method="GET" action="'.$modx->makeUrl(34).'" name="send_query'.$item['id'].'" class="shop2_product_item shop2-product-item ">
	<input type="hidden" name="id_obj" value="'.$item['id'].'">
	<div class="new_labels price">
		<div class="product-flag label_item rent" style="background: #fc906c;">
			Аренда'.$item['address']['station_distance']['id'].'
			<span style="border-bottom: 10px solid #fc906c; border-right: 6px solid transparent;"></span>
		</div>
	</div>
	<div class="shop2_product_inner">
		<div class="product_top_wr">
			<div class="product-top">
				<div class="product-image">
					<a  href="javascript:document.send_query'.$item['id'].'.submit()" >
						<img class="lazyload" data-src="'.$photo_rent_main.'" alt="'.$item['title'].'" title="'.$item['title'].'" src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" />
					</a>
				</div>
				<div class="new_labels">
					<div class="product-flag label_item rent" style="background: #fc906c;">
						Аренда
						<span style=" border-right: 6px solid transparent; border-top: 10px solid #fc906c;"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="product_bot_wr">
			<div class="product_bot_in1">
				<div class="product_in1_left">
					<div class="product-name"><a href="javascript:document.send_query'.$item['id'].'.submit()">'.$item['title'].'</a></div>
					<div class="prod_address">
						'.$item['address']['full_address'].'
					</div>
					<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$rent_on_main .= '

							<li data-kinds="" data-name="metro_station" data-value="">';
			
			
			$check = true;
			if(in_array($item['address']['station']['id'], $metro_colors_b61d8e)){
				$rent_on_main .= '<span class="metro_icon" style="background-color: #b61d8e"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_ef1e25)){
				$rent_on_main .= '<span class="metro_icon" style="background-color: #ef1e25"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_029a55)){
				$rent_on_main .= '<span class="metro_icon" style="background-color: #029a55"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_fbaa33)){
				$rent_on_main .= '<span class="metro_icon" style="background-color: #fbaa33"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}

			if(in_array($item['address']['station']['id'], $metro_colors_019ee0)){
				$rent_on_main .= '<span class="metro_icon" style="background-color: #019ee0"></span><span class="metro_title">'.$item['address']['station']['name'];
				$check = false;
			}
			
			if($check){
				$rent_on_main .= '<span class="metro_title">'.$item['address']['station']['name'];
			}
			if(isset($item['address']['station_distance']['id'])){
				$rent_on_main .= ' - '.$item['address']['station_distance']['name'].'</span></li>';
			}
			else{
				'</span></li>';
			}
		}
		$rent_on_main .= '
					</ul>
				</div>
				<div class="product_in1_right">
					<div class="product-price">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit">'.$item['price_unit']['name'].'/мес.</span>                            	
					</div>
				</div>
			</div>
			<div class="product_bot_in2">
				<div class="product_in2_wrap">
					<div class="pr_list_options">
						<div class="prod_address">
							'.$item['address']['full_address'].'
						</div>
						<ul class="metro_wrap menu-default">';
		if(isset($item['address']['station']['name'])){
			$rent_on_main .= '			
							<li data-kinds="1511470016" data-name="metro_station" data-value="75c7c0d73bc89e692a849cddac7d0fb7"><span class="metro_icon" style="background-color: #cc0000"></span><span class="metro_title">'.$item['address']['station']['name'].'</span></li>';
		}	
		$rent_on_main .= '
						</ul>
					</div>
					<div class="options_title_wr">
						<div class="options_show_btn">
							<div class="show_btn">Параметры</div>
						</div>
					</div>
					<div class="all_options_wrap">
					</div>
				</div>
				<div class="product-bot">
					<div class="product_pricelist">
						<div class="price-current">
							<strong>'.number_format($item['price'], 0, '', ' ').'</strong> <em class="fa fa-rouble"></em>			
						</div>
						<span class="product_unit">/мес.</span>                            	
					</div>
					<a href="javascript:document.send_query'.$item['id'].'.submit()" class="product_link">
						<span>Подробнее</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>';

		
	}
			
}




$modx->setPlaceholder('torg_count',$torg_count);
$modx->setPlaceholder('ofice_count',$ofice_count);
$modx->setPlaceholder('pr_count',$pr_count);
$modx->setPlaceholder('zemuch_count',$zemuch_count);
$modx->setPlaceholder('rent_count',$rent_count);
$modx->setPlaceholder('out_rent_main',$rent_on_main);
$modx->setPlaceholder('sail_objects',$sail_objects);
$modx->setPlaceholder('sail_ofice',$sail_ofice);
$modx->setPlaceholder('sail_torg',$sail_torg);
$modx->setPlaceholder('sail_zemuch',$sail_zemuch);	
$modx->setPlaceholder('sail_pr',$sail_pr);	
$modx->setPlaceholder('out_action',$out_action);
$modx->setPlaceholder('rent_on_main_price',json_encode($rent_on_main_price));
$modx->setPlaceholder('sail_objects_price',json_encode($sail_objects_price));
$modx->setPlaceholder('sail_ofice_price',json_encode($sail_ofice_price));
$modx->setPlaceholder('sail_torg_price',json_encode($sail_torg_price));
$modx->setPlaceholder('sail_zemuch_price',json_encode($sail_zemuch_price));
$modx->setPlaceholder('sail_pr_price',json_encode($sail_pr_price));

/*}*/

//проверяем пост-дата заполненные при клике на объект
if(isset($_GET['id_obj'])){
	foreach($all_items as $obj_ar) {
		if($obj_ar['id'] == $_GET['id_obj']){
			$id_obar = $obj_ar['id'];
			$count_photos = 0;
			$name_obar = $obj_ar['title'];
			$photos_obar = $obj_ar['photos'];
			$layouts_obar = $obj_ar['layouts'];
			if($obj_ar['photos'][0] != null){
				foreach($photos_obar as $photo) {
					$out_photos .= '<div class="product_image">
									<a href="'.$photo.'">
										<img class="lazyload" src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" data-src="'.$photo.'" alt="'.$name_obar.'" title="'.$name_obar.'" />
									</a>
								</div>';
					$count_photos += 1;
				}
			}	
			if($obj_ar['layouts'][0] != null){
				foreach($layouts_obar as $photo) {
					$out_photos .= '<div class="product_image">
									<a href="'.$photo.'">
										<img class="lazyload" src="assets/templates/lider/img/no_photo.jpg"  widh="368"  height="210" data-src="'.$photo.'" alt="'.$name_obar.'" title="'.$name_obar.'" />
									</a>
								</div>';
					$count_photos += 1;
				}
			}
			$price_obar = number_format($obj_ar['price'], 0, '', ' ');
			$unit_obar = $obj_ar['price_unit']['name'];
			$address_obar = $obj_ar['address']['full_address'];
			$lat_obar = $obj_ar['coordinates']['lat'];
			$lng_obar = $obj_ar['coordinates']['lng'];
			$metro_obar = $obj_ar['address']['station']['name'];
			$dist_obar = $obj_ar['address']['station_distance']['name'];
			$desc_obar = $obj_ar['description'];
			$totar_obar = $obj_ar['total_area'];
			if($obj_ar['site_category']['id'] == 2){
				$specialty_name = $obj_ar['site_category']['name'] ; 
				$specialty_url = $modx->makeUrl(24);
			}
			if($obj_ar['site_category']['id'] == 1){
				$specialty_name = $obj_ar['site_category']['name'] ; 
				$specialty_url = $modx->makeUrl(23);
			}
			if($obj_ar['site_category']['id'] == 4){
				$specialty_name = $obj_ar['site_category']['name'] ;  
				$specialty_url = $modx->makeUrl(26);
			}
			if($obj_ar['site_category']['id'] == 3){
				$specialty_name = $obj_ar['site_category']['name'] ;  
				$specialty_url = $modx->makeUrl(25);
			}
			$full_id_obar = $obj_ar['full_id'];
			$od_even = 0;
			
			if(!empty($obj_ar['floor'])){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Этаж</div><div class="param_body">'.$obj_ar['floor']['name'].'</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Этаж</div><div class="param_body">'.$obj_ar['floor']['name'].'</div></div>';;
				}
			}
			if(!empty($obj_ar['floors'])){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Этажность</div><div class="param_body">'.$obj_ar['floors'].'</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Этажность</div><div class="param_body">'.$obj_ar['floors'].'</div></div>';;
				}
			}
			if(!empty($obj_ar['total_area'])){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Общая площадь</div><div class="param_body">'.$obj_ar['total_area'].' м²</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Общая площадь</div><div class="param_body">'.$obj_ar['total_area'].' м²</div></div>';;
				}
			}
			if($obj_ar['ceiling_height'] != 0){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Высота потолков</div><div class="param_body">'.$obj_ar['ceiling_height'].' м</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Высота потолков</div><div class="param_body">'.$obj_ar['ceiling_height'].' м</div></div>';;
				}
			}
			if($obj_ar['lot_area'] != 0){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Площадь участка</div><div class="param_body">'.$obj_ar['lot_area'].' Га</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Площадь участка</div><div class="param_body">'.$obj_ar['lot_area'].' Га</div></div>';;
				}
			}
			if(!empty($obj_ar['condition_type'])){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Состояние</div><div class="param_body">'.$obj_ar['condition_type']['name'].'</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Состояние</div><div class="param_body">'.$obj_ar['condition_type']['name'].'</div></div>';;
				}
			}
			if(!empty($obj_ar['input_type'])){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Тип входа</div><div class="param_body">'.$obj_ar['input_type']['name'].'</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Тип входа</div><div class="param_body">'.$obj_ar['input_type']['name'].'</div></div>';;
				}
			}
			if(!empty($obj_ar['input_count'])){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Количество входов</div><div class="param_body">'.$obj_ar['input_count'].'</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Количество входов</div><div class="param_body">'.$obj_ar['input_count'].'</div></div>';;
				}
			}
			if($obj_ar['phone_lines'] != 0){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Телефонных линий</div><div class="param_body">'.$obj_ar['phone_lines'].'</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Телефонных линий</div><div class="param_body">'.$obj_ar['phone_lines'].'</div></div>';;
				}
			}
			if($obj_ar['has_electricity'] == true){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Есть электричество:</div><div class="param_body">Есть</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Есть электричество:</div><div class="param_body">Есть</div></div>';;
				}
			}
			if(!empty($obj_ar['electricity_power'])){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Электричество</div><div class="param_body">'.$obj_ar['electricity_power'].' кВТ</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Электричество</div><div class="param_body">'.$obj_ar['electricity_power'].' кВТ</div></div>';;
				}
			}
			if($obj_ar['has_water'] == true){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Есть водоснабжение:</div><div class="param_body">Есть</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Есть водоснабжение:</div><div class="param_body">Есть</div></div>';;
				}
			}
			if($obj_ar['has_heating'] == true){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Есть отопление:</div><div class="param_body">Есть</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Есть отопление:</div><div class="param_body">Есть</div></div>';;
				}
			}
			if($obj_ar['has_parking'] == true){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Есть парковка:</div><div class="param_body">Есть</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Есть парковка:</div><div class="param_body">Есть</div></div>';;
				}
			}
			if($obj_ar['has_security'] == true){
				$od_even += 1;
				if($odd_even % 2 == 0){
					$obj_params .= '<div class="param_item odd"><div class="param_title">Есть охрана:</div><div class="param_body">Есть</div></div>';
				}
				else{
					$obj_params .= '<div class="param_item even"><div class="param_title">Есть охрана:</div><div class="param_body">Есть</div></div>';;
				}
			}
		}
	}
	$modx->setPlaceholder('id_obar',$id_obar);
	$modx->setPlaceholder('name_obar',$name_obar);
	$modx->setPlaceholder('photos_obar',$out_photos);
	$modx->setPlaceholder('count_photos_obar',$count_photos);
	$modx->setPlaceholder('price_obar',$price_obar);
	$modx->setPlaceholder('unit_obar',$unit_obar);
	$modx->setPlaceholder('address_obar',$address_obar);
	$modx->setPlaceholder('lat_obar',$lat_obar);
	$modx->setPlaceholder('lng_obar',$lng_obar);
	$modx->setPlaceholder('metro_obar',$metro_obar);
	$modx->setPlaceholder('dist_obar',$dist_obar);
	$modx->setPlaceholder('desc_obar',$desc_obar);
	$modx->setPlaceholder('totar_obar',$totar_obar);
	$modx->setPlaceholder('specialty_name',$specialty_name);
	$modx->setPlaceholder('specialty_url',$specialty_url);
	
	$modx->setPlaceholder('full_id_obar',$full_id_obar);
	$modx->setPlaceholder('obj_params',$obj_params);
	
	
	

	
}
else{
}


if($total_area == true){
	return $totar_obar;
}

