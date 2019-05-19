<?php
session_start();
$product_ids = array();
//session_destroy();



//vlepoume ean to Add to Cart exei patithei
if(filter_input(INPUT_POST, "add_to_cart")){
	if(isset($_SESSION['shopping_cart'])){
		//metraei posa proionta paththikan
		$count = count($_SESSION['shopping_cart']);

		//create squantial array for matching array keys to product ids
		$product_ids = array_column($_SESSION['shopping_cart'], 'id');

		if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)){
		$_SESSION['shopping_cart'][$count] = array
			(
				'id' => filter_input(INPUT_GET, 'id'),
				'name' => filter_input(INPUT_POST, 'name'),
				'price' => filter_input(INPUT_POST, 'price'),
				'quantity' => filter_input(INPUT_POST, 'quantity'),
				
			);
		}
		else {//ean yparxei eidi ayxhse thn posothta
			//taytopihse to array key me to id tou product kai prosthese to sto kalathi
			for($i=0; $i < count($product_ids); $i++){
				if ($product_ids[$i] == filter_input(INPUT_GET,'id')){
					//prosthse posothta sthn yparxousa tou array
					$_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
				}
			}
		}

	}
	else{//ean den yparxei shopping cart dhmiourgoume prwto product me array thesi 0
		 //dimirgia array lambanontas plhrofories,xekinontas apo thesi 0 kai gemizoume me times 
		$_SESSION['shopping_cart'][0] = array
		(
			'id' => filter_input(INPUT_GET, 'id'),
			'name' => filter_input(INPUT_POST, 'name'),
			'price' => filter_input(INPUT_POST, 'price'),
			'quantity' => filter_input(INPUT_POST, 'quantity'),
			
		);
	}
}

if(filter_input(INPUT_GET, 'action') == 'delete'){
	//loop throught all products in the shopping cart until it matches with GET id variable
	foreach ($_SESSION['shopping_cart'] as $key => $product) {
		if ($product['id'] == filter_input(INPUT_GET, 'id')) {
			//remove product from the shopping cart when it matches with the GET id
			unset($_SESSION['shopping_cart'][$key]);
		}
	}
	//reset session array keys so they match with $product_ids numeric array
	$_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']); 

}



 


//pre_r($_SESSION);

function pre_r($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Shopping cart (working)</title>
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="cart.css">
	</head>
	<body>
		<div class="container">
		<?php

		$connect = mysqli_connect('localhost','root','9947026aA','cart');		
		$query = 'SELECT * FROM product ORDER by id ASC';
		$result = mysqli_query($connect,$query);


		if($result):
		    if(mysqli_num_rows($result)>0):
		        while($product = mysqli_fetch_assoc($result)):
		         ?>
		         <div class="col-sm-4 col-md-4">
		         	<form method="post" action="cart.php?action=add&id=<?php echo $product['id']; ?>">
		         		<div class="products">
		         			<img src="<?php echo $product['image']; ?>" class="img-responsive">
		         			<h4 class="text-info"><?php echo $product['name']; ?></h4>
		         			<h4>$ <?php echo $product['price']; ?></h4>
		         			<input type="text" name="quantity" class="form-control" value="1"/>
		         			<input type="hidden" name="name" value="<?php echo $product['name']; ?>">
		         			<input type="hidden" name="price" value="<?php echo $product['price']; ?>">
		         			<input type="submit" name="add_to_cart" style="margin-top: 5px;" class="btn btn-info" value="Add to Cart" />
		         		</div>
		         	</form>
		         </div>
		         <?php
		        endwhile;
		    endif;
		endif;    		
		?>
		<div style='clear:both'></div>
		<br />
		<div class="table-responsive">
		<table class="table">
			<tr><th colspan="5"><h3>Order Details</h3></th></tr>
		<tr>
			<th width="40%">Product Name</th>
			<th width="10%">Quantity</th>
			<th width="20%">Price</th>
			<th width="15%">Total</th>
			<th width="5%">Action</th>
		</tr>
		<?php
		if(!empty($_SESSION['shopping_cart'])):

			$total = 0;

			foreach ($_SESSION['shopping_cart'] as $key => $product) :
			
		?>
		<tr>
			<td><?php echo $product['name']; ?></td>
			<td><?php echo $product['quantity']; ?></td>
			<td><?php echo $product['price']; ?></td>
			<td><?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
			<td>
				<a href="cart.php?action=delete&id=<?php echo $product['id']; ?>">
					<div class="btn btn-danger">Remove</div>
				</a>
			</td>
		</tr>
		<?php
				$total = $total + ($product['quantity'] * $product['price']);
			endforeach;
		?>
		<tr>
			<td colspan="3" align="right">Total</td>
			<td align="right">$ <?php echo number_format($total, 2); ?></td>
			<td></td>
		</tr>
		<tr>
			
			<td colspan="5">
			<?php
				if (isset($_SESSION['shopping_cart'])):
				if (count($_SESSION['shopping_cart']) > 0):
			?>	
				<a href="cart.php?action=checkout&id=<?php echo $product['id']; ?>"
				 class="button">Checkout</a>
			<?php endif; endif; ?>	
			</td>
		</tr>
		<?php
		 endif;
		?>
		</table>	
		</div>
		</div>
	</body>
</html>
