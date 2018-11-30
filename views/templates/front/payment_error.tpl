{extends file='page.tpl'}
{block name='page_content'}
	<form action="{$link->getModuleLink('byjuno', 'validation', [], true)|escape:'html':'UTF-8'}" method="post">
		<p class="warning">
			{l s='Order processing error. Please try again.' mod='byjuno'}
		</p>
		<p class="cart_navigation clearfix" id="cart_navigation">
			<a href="{$link->getPageLink('order', true, NULL, "step=1")|escape:'html':'UTF-8'}" class="btn btn-primary">
				{l s='Other payment methods' mod='byjuno'}
			</a>
		</p>
	</form>
{/block}

