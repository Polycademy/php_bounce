<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div class="container">
			<h1 class="main_heading">Mission List Editor</h1>
			<? if(!empty($status)){ ?>
				<div class="form_status">
					<h4>Form Status Messages:</h4>
					<ul>
						<?=$status?>
					</ul>
				</div>
			<? } ?>
			<nav class="editor_nav">
				<ul class="nav nav-tabs">
					<li><?= anchor('mission_editor', 'Mission Editor List') ?></li>
					<li><?= anchor('mission_editor/add', 'Mission Editor Add') ?></li>
				</ul>
			</nav>
			<div class="mission_list">
				<? if(empty($missions)){ ?>
					<h2>Sorry no missions to show!</h2>
				<? }else{ ?>
					<table class="table table-bordered table-condensed mission_table">
						<thead>
							<tr>
								<th scope="col"><strong>Mission Number</strong></th>
								<th scope="col">Title</th>
								<th scope="col">Description</th>
								<th scope="col">Whitelist</th>
								<th scope="col">Parameters</th>
							</tr>
						</thead>
						<tbody>
						<? foreach($missions as $mission){ ?>
							<tr>
								<th scope="row"><?=anchor('mission_editor/update/' . $mission['id'], $mission['mission_number'])?></th>
								<td><?=$mission['title']?></td>
								<td><?=$mission['description']?></td>
								<td><?=$mission['whitelist']?></td>
								<td class="parameters_td"><pre><code><?=$mission['parameters']?></code></pre></td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				<? } ?>
			</div>
		</div>