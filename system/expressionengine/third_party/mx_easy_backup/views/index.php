<div class="mor">

<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>
				
	

				<?=form_open($_form_base, array('name'=>'tasklist', 'id'=>'tasklist'), '')?>
				
				<?=lang('task_page_info')?>
				
				<?php
					$this->table->set_template($cp_pad_table_template);
					$this->table->set_heading(
												array('data' => form_checkbox('select_all', 'true', FALSE, 'class="toggle_all"'), 'width' => '4%'),
												array('data' => lang('task_name')),
												array('data' => lang('last_run'), 'width' => '120px'),
												array('data' => lang('send_file'), 'width' => '100px'),												
												array('data' => lang('edit'), 'width' => '50px'),
												array('data' => lang('run'), 'width' => '50px')
											);
					if($task_list->num_rows())
					{
						foreach ($task_list->result() as $row)
						{
							$task_settings = unserialize($row->settings);
			
							$this->table->add_row(
													'<input class="toggle" type="checkbox" name="toggle[]" value="'.$row->task_id.'"  />',
													$task_settings['task_name'],
													"<strong>".$this->localize->format_date($datestr, $row->last_run, TRUE)."</strong>",
													lang($task_settings['send_to']),
													'<span class="button"><a title="'.lang('edit').'" class="submit" href="'.$_base.'&method=task&task_id='.$row->task_id.'">'.lang('edit').'</a></span>',
													'<span class="button"><a title="'.lang('run').'" class="submit runtask" href="'.$_base.'&method=make_backup&task_id='.$row->task_id.'">'.lang('run').'</a></span>'
												);
										
						}
						
							$this->table->add_row(
							form_checkbox('select_all', 'true', FALSE, 'class="toggle_all"'),
							array('data' => lang('select_all'), 'colspan' => 6)
						);
					}else{
							$this->table->add_row(array('data' => str_replace("%link",$_base."&method=task",lang('task_list_empty')), 'colspan' => 7));
					}

				?>
				
				<div class=" shun"><?=$this->table->generate()?></div>
				<?=form_submit(array('name' => 'submit', 'value' => lang('delete'), 'class' => 'submit'))?>
				<span class="button" style="float:right;"><a href="<?=$_base?>&method=task" class="submit"><?=lang('create_task') ?></a></span>

				<?=form_close()?>
</div>


<script type="text/javascript">
		jQuery(function() {


			$('#tasklist .runtask').click(function()
			{
				button = $(this);
							
				button.parent().hide().after('<img src="<?=$cp_theme_url?>images/indicator2.gif" style="padding-right:0px;" class="indicator" />');
	
			    $.ajax({
 				 url: button.attr('href'),
  				 success: function(data){
  				 		$('.indicator').remove();
  						button.parent().show()
  						if (data = "OK") {
							alert('<?=lang('backup_success')?>');
						}
						else {
							alert('<?=lang('backup_faild')?>' + data);
						}
  				    }
				}); 
				//alert ("asdasd");
				return false;	
			}); 
		});
</script>	