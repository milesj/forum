    
<?php // Options
if (!empty($this->passedArgs)) {
	$this->Paginator->options(array('url' => $this->passedArgs));
} ?>

<div class="pagination">
	<?php echo $this->Paginator->counter(array('format' => __d('forum', 'Displaying %start%-%end% of %count%', true))); ?>

	<div class="pagingList">
		<?php // Paging 
        echo $this->Paginator->first('&laquo;', array('escape' => false));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->last('&raquo;', array('escape' => false)); ?> 
	</div>
    
	<div class="clear"><!-- --></div>
</div>
