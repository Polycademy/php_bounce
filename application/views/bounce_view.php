<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div class="container">
			<article class="mission_list">
				<h1>Mission List</h1>
				<div class="mission_list_container">
					<? if(!empty($missions)){ ?>
						<? foreach($missions as $mission){ ?>
							<?=anchor('bounce/mission/' . $mission['mission_number'], $mission['title'], array('class'=>'mission'))?>
						<? } ?>
					<? }else{ ?>
						<h2>No missions yet!</h2>
					<? } ?>
				</div>
			</article>
		</div>