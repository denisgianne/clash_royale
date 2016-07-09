<?php
// to not render the table.. just to create a static HTML with data
ob_start();
$log_str = '';
$log_str .= "[userAgent:".$_SERVER['HTTP_USER_AGENT'] ."]\n";
$log_str .= "[ip:".$_SERVER['REMOTE_ADDR']."]\t[datetime:".date('Y-m-d H:i:s')."]\n";
$log_str .= "\n";
error_log( $log_str, 3, 'logs/clash_royale.log');
error_log( $log_str, 3, 'logs/ip/'.$_SERVER['REMOTE_ADDR'].'.log');

Class Troops{
	private $troops = [];
	public function add(Troop $troop){
		$this->troops [$troop->id_troop_level]= $troop;
	}
	public function order($attribute = 'full_name'){
		switch ($attribute) {
			case 'alpha':
			case 'full_name':
				usort($this->troops, [$this, 'orderAlpha']);
			break;
			case 'damage_per_second':
			case 'damage_sec':
			case 'damage_per_second_per_elixir':
				usort($this->troops, [$this, 'orderDamagePerSecond']);
			break;
			case 'damage_per_elixir':
			case 'damage':
				usort($this->troops, [$this, 'orderDamage']);
			break;
			case 'hitpoint':
			case 'hitpoints':
				usort($this->troops, [$this, 'orderHitpoints']);
			break;
			return $this;
		}
	}
	public function each(){
		return each($this->troops)['value'];
	}
	public function current(){
		return current($this->troops);
	}
	public function prev(){
		return prev($this->troops);
	}
	public function reset(){
		reset($this->troops);
	}
	public function count(){
		return count( $this->troops );
	}
	public function orderAlpha($a, $b){
		return strcmp($a->full_name, $b->full_name);
	}
	public function orderInt($a, $b, $field, $second_field = null){
		if ($a->{$field} == $b->{$field}) {
			if($second_field != null){
				if($a->{$second_field} == $b->{$second_field}){
					return 0;
				}
				return ($a->{$second_field} < $b->{$second_field}) ? -1 : 1;
			}
			return 0;
		}
		return ($a->{$field} < $b->{$field}) ? -1 : 1;
	}
	public function orderDamagePerSecond($a, $b){
		return $this->orderInt( $a, $b, 'damage_per_second_per_elixir', 'hitpoint_per_elixir' );
	}
	public function orderDamage($a, $b){
		return $this->orderInt( $a, $b, 'damage_per_elixir' );
	}
	public function orderHitpoints($a, $b){
		return $this->orderInt( $a, $b, 'hitpoint_per_elixir', 'damage_per_second_per_elixir' );
	}
}
Class Troop{
	// public int $count;
	function __construct(){
		// $this->damage = $this->damageAttribute();
		if($this->type == 'troop'){
			$this->full_name = $this->name.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Level '.$this->level;
			// $this->hitpoint_per_elixir = ($this->hitpoints * $this->count) / $this->elixir_cost;
			// $this->damage_per_second_per_elixir = ($this->damage_per_second * $this->count) / $this->elixir_cost;
			$this->hitpoint_per_elixir = (float) $this->hitpoint_per_elixir;
			$this->damage_per_second_per_elixir = (float) $this->damage_per_second_per_elixir;
			// $this->damage_per_elixir = ($this->damage * $this->count) / $this->elixir_cost;
		}
	}
	// function __get( $attr ){
	// 	if(in_array($attr, ['damage']))
	// 		return $this->{$attr.'Attribute'}();
	// 	return null;
	// }
	function damageAttribute(){
		return ceil($this->damage_per_second * $this->hit_speed);
	}
	function tag($field){
		return '<'.$field.'>'.$this->{$field}.'</'.$field.'>';
	}
	function tag_multiplier($field, $multiplier = 1, $complement = ''){
		return '<'.$field.'>'.ceil($this->{$field}*$multiplier).$complement.'</'.$field.'>';
	}
	function compare_field($field, Troop $troop){
		$diff = number_format( (1-($troop->{$field}/$this->{$field}))*100, 2 );
		return '<compare class="'.($diff == 0 ? 'equal' : ($diff > 0 ? 'up' : 'down')).'"> '.$diff.'%</compare>';
	}
}
?><html>
	<head>
		<title>Clash Royale</title>
		<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
		<script>
		function	alter_lines(){
			var count = 1;
			$('.alternate').each(function(index, el) {
				if( !$(this).is(':hidden') ){
					if(count%2==0){
						$(this).addClass('bg1');
					}else{
						$(this).removeClass('bg1');
					}
					count++;
				}
			});
		}
$(document).ready(function(){
	alter_lines();
});
		</script>
<style>
body{background-color: #fff; padding: 44px 0 0 3px; margin: 0;font-size: 12px;}
#tabela_tropas{ float: left; width: 716px;}
.top{position: fixed; background: #fff; top: 0px;}
.troop_header{width: 710px; }
.troop_column{float: left;display: block; width: 227px;}
.troop_stats{ height: 35px; float: left; }
.troop_stats:hover{ background-color: #D6EAF9;}
.bg1{background-color: #eee;}
.row{ float: left; }
.l0{background-color: #eeeeee; }
row_name{float: left;display: block; margin-right: 5px; }
name_troop{float: left; display: block; width: 113px; border-left: 1px solid black; padding-left: 3px;}
full_name{font-size:12px;font-weight: bold;}
hitpoints, damage_per_second, damage,
hitpoint_per_elixir, damage_per_second_per_elixir, damage_per_elixir{
	/*margin-right: 10px;*/
	width: 55px;
	display: block;
	float: left;
}
hitpoints, hitpoint_per_elixir, hitpoints a{color:red;}
damage_per_second, damage_per_second_per_elixir, damage_per_second a{ color:orange; }
damage,damage_per_elixir, damage a{ color:orangered; }
.common, .rare, .epic, .legendary{vertical-align: middle;}
.common{color:blue;}
.rare{color: orangered;}
.epic{color:purple;}
.legendary,.rainbowize{
  background-image: -webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );
  background-image: gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );
  background-image: -moz-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );
  -webkit-background-clip: text;
  background-clip: text;
  /*color: green;*/
  /*color: transparent;*/
  color: rgba(0, 200, 0, 0.5);
}
compare{
	display: block;
}
compare.up{color: green;}
compare.down{color: red;}
compare.equal{display: none;}
</style>
	</head>
	<body>
<?php
$legendas_campos = [
	'full_name',
	'hitpoints',
	'damage_sec',
	'damage'
];
// $pdo = new PDO('mysql:host=mysql.potpracy.com.br;dbname=teste_pot', 'teste_pot', 'teste2011',[PDO::ATTR_EMULATE_PREPARES => false]);
$pdo = new PDO('mysql:host=localhost;dbname=clash_royale', 'root', '',[PDO::ATTR_EMULATE_PREPARES => false]);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$ob = [
	'alpha'				=> 'troops.name, troops_levels.level',
	'damage_sec'	=> 'damage_per_second_per_elixir, hitpoint_per_elixir, troops_levels.level',
	'damage'			=> 'damage_per_elixir, hitpoint_per_elixir, troops_levels.level',
	'hitpoint'		=> 'hitpoint_per_elixir, damage_per_second_per_elixir, troops_levels.level'
];
// $ob = (array_key_exists('ob', $_GET) && array_key_exists($_GET['ob'], $ob)) ? $ob[$_GET['ob']] : $ob['alpha'];
$ob = $ob['alpha'];
$q = $pdo->query('SELECT *, troops_levels.id as id_troop_level,
	((troops_levels.hitpoints*troops.count) / troops.elixir_cost) as hitpoint_per_elixir,
	((troops_levels.damage_per_second*troops.count) / troops.elixir_cost) as damage_per_second_per_elixir,
	troops_levels.damage_per_second*troops.hit_speed as damage,
	((troops_levels.damage_per_second*troops.hit_speed*troops.count) / troops.elixir_cost) as damage_per_elixir
	FROM troops_levels
	LEFT JOIN troops ON
	troops.id=troops_levels.id_troop
	-- WHERE (
	-- 	( troops.rarity = "common" AND troops_levels.level BETWEEN 5 AND 8 )
	-- 	OR ( troops.rarity = "rare" AND troops_levels.level BETWEEN 3 AND 6 ) 
	-- 	OR ( troops.rarity = "epic" AND troops_levels.level BETWEEN 1 AND 4 )
	-- 	OR ( troops.rarity = "legendary" AND troops_levels.level BETWEEN 1 AND 2 )
	-- )
	-- AND damage_type="area"
	-- ORDER BY '.$ob.'
	ORDER BY troops.name, troops_levels.level
	');
$elixir = 1;
$obs = ['alpha' => 'Name', 'damage_sec' => 'Damage per Second', 'hitpoints' => 'Hipoints'];
?>
<div id="tabela_tropas">
<div class="top">
	<div class="troop_header">
		<full_name>
		Updated at: <?php echo ( new DateTime )->format('Y-m-d');?>
		(<?php echo $elixir; ?> elixir) Proportion<br/>
		</full_name>
	</div>
	<div class="troop_header">
		<?php foreach ($obs as $legenda): ?>
			<name_troop>
				<full_name>Troops by<br/><?php echo $legenda; ?></full_name>
			</name_troop>
			<div class="row">
				<hitpoints>Hitpoint</hitpoints>
				<damage_per_second>Damage sec/</damage_per_second>
				<!-- <damage>Damage</damage> -->
			</div>
		<?php endforeach ?>

		<!-- <name_troop>
			<full_name>Troops by<br/>Damage</full_name>
		</name_troop>
		<div class="row">
			<hitpoints>Hitpoint</hitpoints>
			<damage_per_second>Damage sec/</damage_per_second>
			<damage>Damage</damage>
		</div> -->
	</div>
</div>
<?php
$troops = new Troops;
while ($troop = $q->fetchObject( 'Troop' ) ) {
	$troops->add( $troop );
}

// var_dump($troops->current());

$troops_alpha = clone $troops;
$troops->order('damage_sec');
$troops_damage_sec = clone $troops;
$troops->order('damage');
$troops_damage = clone $troops;
$troops->order('hitpoints');
$troops_hitpoints = clone $troops;


$total = $troops->count();
// $total = 10;
// $last_troop = $troop = [0=>null, 1=>null, 2=>null, 3=>null];
foreach ($obs as $ob => $v) {
	$last_troop = $troop = null;
	echo '<div class="troop_column">';
	// ${'troops_'.$ob}->reset();
	// var_dump(${'troops_'.$ob}->count(), 'total');
	for ($i=1; $i <= $total; $i++) {
		$last_troop = $troop;
		$troop = ${'troops_'.$ob}->each();
		if( ($ob == 'alpha') && ($last_troop != null) && ($last_troop->id_troop != $troop->id_troop))
			$last_troop = null;
		echo '<div class="troop_stats alternate" troop="'.$troop->id_troop.'" level="'.$troop->level.'">';
		echo '<name_troop class="'.$troop->rarity.'">'.$troop->tag('full_name').' (x'.$troop->count.')</name_troop>';
		
		echo '<div class="row">';
		foreach (['hitpoint_per_elixir','damage_per_second_per_elixir'] as $field) {
			$complement = ( $last_troop != null ) ? $troop->compare_field($field, $last_troop) : '';
			echo $troop->tag_multiplier($field, $elixir, $complement);
		}
		echo '</div></div>';
	}
	echo '</div>'."\n\n";
}

echo '</div>';// END: tabela tropas

include_once('filtro.php');

$html = ob_get_contents();
ob_flush();
ob_clean();

$file = fopen('tabela.html','w');
fwrite($file,$html);
fclose($file);