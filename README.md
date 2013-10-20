<h1>Multicart for codeigniter</h1>

This library needs to use the php native sessions, including My_Session.php file in the folder and they are extended libraries correctly.

<h2>Installation</h2>

Download files hosted on libraries folder and place them in the libraries folder of your project.

<h1>Example Usage MultiCart</h1>
<h2>Create instances</h2>
```php
	$this->load->library("udp_cart");//load library
    $this->shop1 = new Udp_cart("shop1");//cart1
    $this->shop2 = new Udp_cart("shop2");//cart2
    $this->shop3 = new Udp_cart("shop3");//cart3
```
<h2>Insert a product</h2>
```php
	$article = array("id" => rand(1,10),"qty" => mt_rand(1,10),"name" => "shoes","price" => "10");
    $article["options"] = array("color" => "black", "size" => "4");

    $this->shop1->insert($article);
```
<h3>Remove a product by rowid</h3>
<p>You just need to pass a rowid that there</p>
```php
	$this->shop1->remove_item("d81d234f1250c815641edb236f705ed5");
```
<h3>Get cart content</h3>
```php	
	$this->shop1->get_content();
```
<h3>Get total cost</h3>
```php	
	$this->shop1->total_cart();
```
<h3>Get total items</h3>
```php	
	$this->shop1->total_articles();
```
<h3>Destroy shop1 instance cart</h3>
```php
	$this->shop1->destroy();
```

<h1>Complete example usage</h1>
```php
class Multi extends CI_Controller
{
    //load library and create new instances
    public function __construct()
    {
        parent::__construct();
        $this->load->library("udp_cart");//load library
        $this->shop1 = new Udp_cart("shop1");//cart1
        $this->shop2 = new Udp_cart("shop2");//cart2
        $this->shop3 = new Udp_cart("shop3");//cart3
    }

    //remove instance shopping cart
    public function destroy()
    {
        if($this->shop1->destroy())
        {
            var_dump($this->shop1);
            echo "<br />";
            echo "The shopping cart was succesful deleted";
        }
    }

    //send an instance to a view
    public function toView()
    {
        $data["shop2"] = $this->shop2;

        $this->load->view("toview", $data);
    }

    //would see instance name shopping cart?
    public function name_instance()
    {
        echo $this->shop2->instance_name();
    }

    //delete a product by rowid
    public function remove_producto()
    {

        if($this->shop1->remove_item("d81d234f1250c815641edb236f705ed5"))
        {
            echo "<br />";
            echo "The product was succesful deleted";
        }   
    }

    public function index()
    {

        echo "<pre>";

        $this->shop1->insert($this->insert());

        if($cart = $this->shop1->get_content())
        {
            foreach($cart as $product)
            {
                echo "<h2>Article</h2>" . PHP_EOL;

                echo "Id product: " .  $product["id"] . PHP_EOL;

                echo "Unique ID product: " .  $product["rowid"] . PHP_EOL;

                echo "Qty product: " .  $product["qty"] . PHP_EOL;

                echo "Price product: " .  $product["price"] . PHP_EOL;

                echo "Product name: " .  $product["name"] . PHP_EOL;

                //if shopping cart has options... loop
                if($this->shop1->has_options($product["rowid"]))
                {
                    foreach($product["options"] as $key => $val) 
                    {
                        echo $key . ": " . $val . PHP_EOL;
                    }
                }
                else
                {
                    echo "Sin opciones" . PHP_EOL;
                }

                //total price sum this articles
                echo "Sum articles: " .  $product["total"] . PHP_EOL . PHP_EOL . PHP_EOL;
            }

            echo "<h1>Total price and articles</h1>" . PHP_EOL;

            echo "Total price: " . $this->shop1->total_cart() . PHP_EOL;

            echo "Total articles: " . $this->shop1->total_articles() . PHP_EOL . PHP_EOL;
        }

        //articles number shopping cart
        echo $this->shop1->total_articles() . PHP_EOL;

        //total price shopping cart
        echo $this->shop1->total_cart() . PHP_EOL;;

    }

    public function show_carts()
    {

        
        var_dump($this->shop1);

        echo "<br /><br />";

        echo $this->shop1->total_articles() . PHP_EOL;

        echo $this->shop1->total_cart() . PHP_EOL . PHP_EOL;

        echo "<br /><br />";

        //nueva instancia

        var_dump($this->shop2);

        echo "<br /><br />";

        echo $this->shop2->total_articles() . PHP_EOL;

        echo $this->shop2->total_cart();

        echo "<br /><br />";


        var_dump($this->shop3);

        echo "<br /><br />";

        echo $this->shop3->total_articles() . PHP_EOL;

        echo $this->shop3->total_cart() . PHP_EOL;

    }

    private function insert()
    {
        $article = array("id" => rand(1,10), "qty" => mt_rand(1,10), "name" => "shoes", "price" => "10");
        $article["options"] = array("color" => "black", "size" => "4");
        return $article;
    }

    public function update()
    {
        
        $article = array("id" => 7, "qty" => 1, "name" => "shoes", "price" => 2);
        $article["options"] = array("color" => "black", "size" => "4");
        
        //if cart is updated show info
        if($this->shop1->update($article))
        {
            var_dump($this->shop1);

            echo $this->shop1->total_articles();

            echo "<br />";

            echo $this->shop1->total_cart();

            echo "<br /><br />";

            echo "The product was succesful updated";
        }   
    }
}
```
## Visit me

* [Visit me](http://uno-de-piera.com)