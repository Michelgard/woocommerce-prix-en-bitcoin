Ajout du code dans cette fonction du fichier class-wc-product-variable.php dans pluging/woocommerce/include
Code ajouté de la ligne 21 à 31 et modification de la ligne 51

/**
	 * Returns an array of data for a variation. Used in the add to cart form.
	 *
	 * @since  2.4.0
	 * @param  WC_Product $variation Variation product object or ID.
	 * @return array|bool
	 */
	public function get_available_variation( $variation ) {
		if ( is_numeric( $variation ) ) {
			$variation = wc_get_product( $variation );
		}
		if ( ! $variation instanceof WC_Product_Variation ) {
			return false;
		}
		// See if prices should be shown for each variation after selection.
		$show_variation_price = apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === '' || $this->get_variation_sale_price( 'min' ) !== $this->get_variation_sale_price( 'max' ) || $this->get_variation_regular_price( 'min' ) !== $this->get_variation_regular_price( 'max' ), $this, $variation );
        
        $price = $variation->get_price();
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
        
		return apply_filters(
			'woocommerce_available_variation', array(
				'attributes'            => $variation->get_variation_attributes(),
				'availability_html'     => wc_get_stock_html( $variation ),
				'backorders_allowed'    => $variation->backorders_allowed(),
				'dimensions'            => $variation->get_dimensions( false ),
				'dimensions_html'       => wc_format_dimensions( $variation->get_dimensions( false ) ),
				'display_price'         => wc_get_price_to_display( $variation ),
				'display_regular_price' => wc_get_price_to_display( $variation, array( 'price' => $variation->get_regular_price() ) ),
				'image'                 => wc_get_product_attachment_props( $variation->get_image_id() ),
				'image_id'              => $variation->get_image_id(),
				'is_downloadable'       => $variation->is_downloadable(),
				'is_in_stock'           => $variation->is_in_stock(),
				'is_purchasable'        => $variation->is_purchasable(),
				'is_sold_individually'  => $variation->is_sold_individually() ? 'yes' : 'no',
				'is_virtual'            => $variation->is_virtual(),
				'max_qty'               => 0 < $variation->get_max_purchase_quantity() ? $variation->get_max_purchase_quantity() : '',
				'min_qty'               => $variation->get_min_purchase_quantity(),
				'price_html'            => $show_variation_price ? '<span class="price">' . $variation->get_price_html() . ' &rarr; ' . $price_html_btc . '<span><b>&#8383;</b></span>' . '</span>' : '',
				'sku'                   => $variation->get_sku(),
				'variation_description' => wc_format_content( $variation->get_description() ),
				'variation_id'          => $variation->get_id(),
				'variation_is_active'   => $variation->variation_is_active(),
				'variation_is_visible'  => $variation->variation_is_visible(),
				'weight'                => $variation->get_weight(),
				'weight_html'           => wc_format_weight( $variation->get_weight() ),
			), $this, $variation
		);
	}
