<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * Library with multiple instances for codeigniter shopping cart
 *
 * @author		Israel Parra
 * @link		http://uno-de-piera.com
 * @since		Version 1.0
 * @filesource
 */

class Udp_cart
{

	// ------------------------------------------------------------------------
	/**
    * Content cart
    * @access private
    * @var array
    */
	private $cart = array();

	// ------------------------------------------------------------------------

	/**
    * instance cart
    * @access private
    * @var string
    */
	private $instance;

	// ------------------------------------------------------------------------

	/**
    * @access public  
    * @param  $var
    * @return instance superobject codeigniter
    */
	public function __get($var)
	{
		return get_instance()->$var;
	}

    /**
    * return name instance
    * @access public  
    * @param  $instance
    * @return name instance
    */
    public function instance_name()
    {
        return $this->instance;
    }

	/**
    * constructor shopping cart, initialize new instance or get other
    * @access public  
    * @param  $instance - instance shopping cart
    */
	public function __construct($instance = "udp")
	{
		//session name for each cart instance
		$this->instance = "udpCart" . $instance;

		//load session library if not is loaded
		!$this->load->library('session') ? $this->load->library('session') : FALSE;

		//if not exists instance set default values
		if ($this->session->userdata($this->instance) === FALSE)
		{
			$this->cart[$this->instance]['total_price'] = 0;
			$this->cart[$this->instance]['total_articles'] = 0;
		}

		//asign to array cart instance session cart
		$this->cart[$this->instance] = $this->session->userdata($this->instance);
	}

	 /**
     * Insert a product into cart.
     * @access public  
     * @param  $items - array contains data item
     * @param  boolean   $update - check if is a insert or update, default insert
     */
	 public function insert($items = array(), $update = FALSE)
	 {
        //required fields for create cart
	 	if(!isset($items["id"]) || !isset($items["qty"]) || !isset($items["price"]))
	 	{
	 		throw new Exception("Id, qty and price are required fields!.", 1);
	 	}

        //Id, qty and price need to be numeric type fields
	 	if(!is_numeric($items["id"]) || !is_numeric($items["qty"]) || !is_numeric($items["price"]))
	 	{
	 		throw new Exception("Id, qty and price must be numbers.", 1);
	 	}

        //items must be an array
	 	if(!is_array($items) || empty($items))
	 	{
	 		throw new Exception("The last row insert method must be an array", 1);        
	 	}

	 	$rowid = $this->_insert($items, $update);

	 	if($rowid)
	 	{
	 		$this->save_cart();
	 		return TRUE;
	 	}else{
	 		throw new Exception("Error saving cart", 1);        
	 	}
	 }

     /**
     * Return rowid.
     * @access private 
     * @param  $items - array contains data item           
     * @param  boolean   $update - check if is a insert or update, default insert
     * @return string
     */
     private function _insert($items = array(), $update = FALSE)
     {
        //check if product has options
     	if (isset($items['options']))
     	{

     		$rowid = md5($items['id'].implode('', $items['options'])); 

     	}else{

     		$rowid = md5($items["id"]);

     	}

     	$items["rowid"] = $rowid;

        //if not empty cart
     	if(!empty($this->cart[$this->instance]))
     	{

            //loop the cart contents
     		foreach($this->cart[$this->instance] as $row)
     		{
                //if this product exists in the cart update !
     			if($row["rowid"] == $rowid && $update == FALSE)
     			{
     				$items["qty"] = $row["qty"] + $items["qty"];
     			}
     		}
     	}

     	$items["qty"] = trim(preg_replace('/([^0-9\.])/i', '', $items["qty"]));

     	$items["price"] = trim(preg_replace('/([^0-9\.])/i', '', $items["price"]));

     	$items["total"] = $items["qty"] * $items["price"];

     	$this->_unset_row($rowid);

     	$this->cart[$this->instance][$rowid] = $items;

     	return $rowid;
     }


    /**
    * store total_articles and total_cart into instance array cart
    * @access private 
    */
    private function save_cart()
    {
    	$total = 0;
    	$items = 0;

    	foreach ($this->cart[$this->instance] as $row) 
    	{
    		$total += ($row['price'] * $row['qty']);
    		$items += $row['qty'];
    	}

    	$this->cart[$this->instance]["total_articles"] = $items;
    	$this->cart[$this->instance]["total_cart"] = $total;
    	$this->get_content();
    }

    /**
    * Return total money cart.
    * @access public 
    * @return int or float
    */
    public function total_cart()
    {
    	if(!isset($this->cart[$this->instance]["total_cart"]) || $this->cart[$this->instance] === NULL)
    	{
    		return 0;
    	}
        //check if total cart not is numeric and the cart not is null
    	if(!is_numeric($this->cart[$this->instance]["total_cart"]))
    	{
    		throw new Exception("The total cart must be an numbers", 1);        
    	}
    	return $this->cart[$this->instance]["total_cart"] ? $this->cart[$this->instance]["total_cart"] : 0;
    }

    /**
    * Return total articles cart.
    * @access public 
    * @return int
    */
    public function total_articles()
    {
    	if(!isset($this->cart[$this->instance]["total_articles"]) || $this->cart[$this->instance] === NULL)
    	{
    		return 0;
    	}
        //check if total articles not is numeric and the cart not is null
    	if(!is_numeric($this->cart[$this->instance]["total_articles"]))
    	{
    		throw new Exception("The total articles must be an numbers", 1);        
    	}
    	return $this->cart[$this->instance]["total_articles"] ? $this->cart[$this->instance]["total_articles"] : 0;
    }

    /**
    * Update row from cart by rowid
    * @access public 
    * @param $item - array contains data item                 
    * @return bool
    */
    public function update($item = array())
    {
        //if not exists cart
    	if($this->cart[$this->instance] === NULL)
    	{
    		throw new Exception("Cart does not exist!", 1);
    	}

        //check if product has options
    	if (isset($item['options']))
    	{
    		$rowid = md5($item['id'].implode('', $item['options'])); 
    	}else{
    		$rowid = md5($item["id"]);
    	}

        //if not exists rowid into cart
    	if(!isset($this->cart[$this->instance][$rowid]))
    	{
    		throw new Exception("The rowid $rowid does not exist!", 1);
    	}

        //if item rowid not equals rowid cart
    	if($rowid !== $this->cart[$this->instance][$rowid]["rowid"])
    	{
    		throw new Exception("Can not update the options!", 1);
    	}

        //param TRUE insert method ¡¡¡IMPORTANT FOR UPDATE, DEFAULT IS FALSE!!!!
    	$this->insert($item, TRUE);
    	$this->get_content();
    	return TRUE;
    }

    /**
    * Return content cart or null if cart is empty
    * @access public 
    * @return array or null
    */
    public function get_content()
    {
		//retorna el contenido de la instancia del carrito
    	$this->session->set_userdata($this->instance,$this->cart[$this->instance]);

    	$cart = $this->session->userdata($this->instance);

    	//nedd remove this keys for correct loop get_content()
    	unset($cart['total_cart']);
    	unset($cart['total_articles']);

    	return $cart == NULL ? NULL : $cart;
    }

    /**
    * Check if cart item has options
    * @access public
    * @param $rowid - rowid contains references item would remove         
    * @return bool
    */
    public function has_options($rowid = '')
    {
    	if(!isset($this->cart[$this->instance][$rowid]['options']) || count($this->cart[$this->instance][$rowid]['options']) === 0)
    	{
    		return FALSE;
    	}

    	return TRUE;
    }

    /**
    * Remove row from cart.
    * @access public
    * @param $rowid - rowid contains references item would remove         
    * @return bool
    */
    public function remove_item($rowid = '')
    {
        //if not exists cart
    	if($this->cart[$this->instance] === NULL)
    	{
    		throw new Exception("Cart does not exist!", 1);
    	}

    	if(!isset($this->cart[$this->instance][$rowid]))
    	{
    		throw new Exception("The rowid $rowid does not exist!", 1);
    	}

    	$this->_remove_item($rowid);
    	$this->get_content();
    	return TRUE;
    }

    /**
    * Remove row from cart.
    * @access private 
    * @param  $rowid - $rowid        
    */
    private function _remove_item($rowid = '')
    {
    	$this->_unset_row($rowid);
    	$this->save_cart();
    }

    /**
    * Remove row from cart by rowid key.
    * @param  $rowid - string contains product rowid        
    * @access private 
    */
    private function _unset_row($rowid = '')
    {
    	unset($this->cart[$this->instance][$rowid]);
    }

    /**
    * Destroy cart.
    * @access private  
    */
    private function _destroy()
    {
    	$this->session->unset_userdata($this->instance);
    	$this->cart[$this->instance] = NULL;
    }

    /**
    * Destroy cart.
    * @access public  
    * @return bool
    */
    public function destroy()
    {
    	$this->_destroy();
    	return TRUE;
    }
}
/**
* file saved in: application/libraries/udp_cart.php
* end shopping cart udp_cart.php
*/

