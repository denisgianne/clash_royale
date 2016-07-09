<style>
	#filtros{
		border-bottom: 1px solid black;
		float: left;
		width: 372px;
		height: 1600px;
	}
	#filtros name_troop{cursor: pointer;}

	levels{ display: block; float: left; width: 248px; }
	level{display: block; float:left; width: 18px;text-align: center;line-height:10px; height: 35px;}
	.alternate{float: left;}
	#troops, #arena, #rarity{ margin: 10px 0 10px 0; }

	#troops{border: 1px dashed black; display: block; float: left;  }
	
	#arena{border: 1px dashed black; display: block; float: left; width: 184px; padding: 3px;}
	#arena label{ display: inline-table; width: 154px;}
	
	#rarity{border: 1px dashed black; display: block; float: left; width: 290px; padding: 3px;}
	rarity{float: left;display: block; width: 65px; height: 35px;}
	#rarity .row{width: 218px;}
</style>
<!-- need to separate CSS scripts here -->
<script>
function off_troop(troop){
	$('div[troop='+troop+']').hide();
	$('#troops  input[troop='+troop+']').prop('checked', false);
}
function on_troop(troop){
	$('div[troop='+troop+']').show();
	$('#troops input[troop='+troop+']').prop('checked', true);
}
	$(document).ready(function(){
		$('#troops input[troop]').change(function(event) {
			var troop = $(this).attr('troop');
			var level = $(this).attr('level');
			var check = $(this).is(':checked');
			if( check ){
				$('div[troop='+troop+'][level='+level+']').show();
			}else{
				$('div[troop='+troop+'][level='+level+']').hide();
			}
			// alter_lines();
		});
		$('#filtros name_troop').click(function(event) {
			var troop = $(this).attr('troop');
			if( $('input[troop='+troop+']:checked').length == 0 ){
				on_troop(troop);
			}else{
				off_troop(troop);
			}
		});
		$('input[name=arena]').click(function(event) {
			var arena = $(this).val();
			$('#filtros name_troop').each(function(index, el) {
				var troop_arena = $(this).attr('arena');
				var troop = $(this).attr('troop');
				if( troop_arena > arena ){
					off_troop( troop );
				}else{
					on_troop(troop);
				}
			});
			alter_lines();
		});

		$('input[name=rarity]').click(function(event) {
			var rarity = $(this).attr('rarity');
			var level = $(this).attr('level');
			var check = $(this).is(':checked');
			var arena = $('input[name=arena]:checked').val();
			$('#troops input[level='+level+'][rarity='+rarity+']').each(function(index, el) {
				console.log('arena:' + arena + '-' + $(this).attr('arena'), check, $(this).prop('checked'), rarity );
				if( arena >= $(this).attr('arena') ){
					console.log('arena aceita');
					if( $(this).prop('checked') != check ){
						console.log('então me clica!');
						$(this).click();
					}
				}
			});
			alter_lines();
		});
	});
</script>
<?php
$levels_rarity = [
	'common'		=> 12,
	'rare'			=> 10,
	'epic'			=> 8,
	'legendary'	=> 6
];
$troops->order('alpha');
$last_troop = null;
$filtros = '';
for ($i=1; $i <= $total; $i++) {
	$troop = $troops->each();
	if($troop->name != $last_troop){
		$filtros .= '<div class="alternate"><name_troop troop="'.$troop->id_troop.'" class="'.$troop->rarity.'" arena="'.$troop->arena.'">'.$troop->tag('name').'<br/>(x'.$troop->count.') '.ucfirst($troop->damage_type).'</name_troop>';
		$filtros .= '<div class="row"><levels>';
		$range = range(1, $levels_rarity[$troop->rarity]);
		foreach ($range as $v) {
			$filtros .= '<level> &nbsp;'.$v.'<br/><input type="checkbox" checked="checked" rarity="'.$troop->rarity.'" troop="'.$troop->id_troop.'" level="'.$v.'" arena="'.$troop->arena.'" value="'.$v.'" /></level>';
		}
		$filtros .= '</levels></div></div>';
	}
	$last_troop = $troop->name;
}
?>
<div id="filtros">
	<div id="troops">
		<?php echo $filtros; ?>
	</div>
	<div id="arena">
		Arena<br/>
		<input type="radio" name="arena" id="arena_1" value="1" /> <label for="arena_1">(Arena 1) Training Camp</label> 
		<input type="radio" name="arena" id="arena_2" value="2" /> <label for="arena_2">(Arena 2) Goblin Stadium</label>
		<input type="radio" name="arena" id="arena_3" value="3" /> <label for="arena_3">(Arena 3) Bone Pit</label>
		<input type="radio" name="arena" id="arena_4" value="4" /> <label for="arena_4">(Arena 4) Barbarian Bowl</label>
		<input type="radio" name="arena" id="arena_5" value="5" /> <label for="arena_5">(Arena 5) P.E.K.K.A Playhouse</label>
		<input type="radio" name="arena" id="arena_6" value="6" /> <label for="arena_6">(Arena 6) Spell Valley</label>
		<input type="radio" name="arena" id="arena_7" value="7" /> <label for="arena_7">(Arena 7) Builder's Workshop</label>
		<input type="radio" name="arena" id="arena_8" value="8" checked="checked" /> <label for="arena_8">(Arena 8) Royal Arena</label>
	</div>
	<div id="rarity">
		Rarity<br/>
		<?php
		foreach ($levels_rarity as $rarity => $max_level) {
			echo '<rarity class="'.$rarity.'">'.ucfirst($rarity).'</rarity><div class="row alternate">';
			$range = range(1, $max_level);
			foreach ($range as $v): ?>
				<level> &nbsp;<?=$v;?><br/>
					<input type="checkbox" name="rarity" checked="checked" rarity="<?=$rarity;?>" level="<?=$v;?>" value="<?=$v;?>" /></level>
			<?php endforeach;
			echo '</div>';
		}?>
	</div>
	area single
</div>
<?php
/*
Atualizar comparadores
atualizar linhas alternadas somente no final
tropas de área / single
tropas por raridade + tropas por range de level
Atualizar as estatísticas
*/