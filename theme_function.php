//****************DEUX FONCTIONS A METTRE DANS LE FICHIER FUNCTION.PHP DE VOTRE THEME *****************


// affichage prix pour les produits simples
add_filter( 'woocommerce_get_price_html', 'sv_change_product_html', 10, 2 );
function sv_change_product_html( $price_html, $product ) {
    //var_dump($product);
    $product_id = $product->get_type();
    //print_r($product_id);
	
	if ($product_id == 'simple') {
        $price = $product->get_price();
        //echo $price;
        $url = "https://bitpay.com/api/rates";
        $json = file_get_contents($url);
        $data = json_decode($json, TRUE); 
        foreach($data as $row) {
            if ($row['code'] == 'EUR') {
                $rate = $row['rate'];
                $price_html_btc = number_format($price / $rate, 8 );
            }
        }
        $price = '<ins>' . wc_price($price) . '</ins>' . ' &rarr; ' . '<ins>' . $price_html_btc . '<span><b>&#8383;</b></span>' . '</ins>';
        $prefix2 = sprintf( __('Prix : ', 'woocommerce'), $price);
		return sprintf('%s%s', $prefix2, $price);	
	}	
	return $price_html;
}

//Affichage des prix en bitcoin pour les produits multiple avec l'affichage des prix à partir de :
add_filter( 'woocommerce_variable_sale_price_html', 'iconic_variable_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'iconic_variable_price_format', 10, 2 );
function iconic_variable_price_format( $price, $product ) {

    $min_price_regular = $product->get_variation_regular_price( 'min', true );
    $min_price_sale = $product->get_variation_sale_price( 'min', true );
    $max_price = $product->get_variation_price( 'max', true ); 
    $min_price = $product->get_variation_price( 'min', true );
    
    $prefix = sprintf( __( 'A partir de :', 'woocommerce' ), wc_price( $min_price ) );
    $prefix2 = sprintf( __( 'Prix : ', 'woocommerce' ), wc_price( $min_price ) );
    
    $url = "https://bitpay.com/api/rates";
    $json = file_get_contents($url);
    $data = json_decode($json, TRUE); 
    foreach($data as $row) {
        if ($row['code'] == 'EUR') {
            $rate = $row['rate'];
            $min_price_btc = number_format( $min_price / $rate, 8 );
            $max_price_btc = number_format( $max_price / $rate, 8 );
            $min_price_regular_btc = number_format( $min_price_regular / $rate, 8 );
            $min_price_sale_btc = number_format( $min_price_sale / $rate, 8 );
       }
    }

    if($min_price_sale == $min_price_regular){
        $price = wc_price($min_price_regular) . '<br/>' . $min_price_regular_btc . '<span><b>&#8383;</b></span>';
    }else{
        $price = '<del>' . wc_price($min_price_regular) . '</del>' . '  <ins>' . wc_price($min_price_sale) . '</ins> <br/>' .
            '<del>' . $min_price_regular_btc . '<span><b>&#8383;</b></span></del>' .  '  <ins>' . $min_price_sale_btc . '<span><b>&#8383;</b></span></ins>';
    }
    
    if( $min_price == $max_price ){
        return sprintf('%s%s', $prefix2, $price); 
    }else{
        return sprintf('%s%s', $prefix, $price);
    } 
}
