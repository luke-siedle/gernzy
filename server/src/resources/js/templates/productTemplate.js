const productTemplate = ({ title, short_description, id, buttonText, price_cents, price_currency, quantity }) => `
<div>
    <div class="uk-card uk-card-default uk-margin-left uk-margin-top">
        <div class="uk-card-header">
            <div class="uk-grid-small uk-flex-middle" uk-grid>
                <div class="uk-width-auto">
                    <//img class="uk-border-circle" width="40" height="40" src="images/avatar.jpg">
                    <span uk-icon="icon: camera"></span>
                </div>
                <div class="uk-width-expand">
                    <h3 class="uk-card-title uk-margin-remove-bottom product-title" id="product-title-${id}">${title}</h3>
                    <p class="uk-text-meta uk-margin-remove-top"><time datetime="2016-04-01T19:00">April 01, 2016</time></p>
                </div>
            </div>
        </div>
        <div class="uk-card-body">
            <p class="short-description">${short_description}</p>
            <hr class="uk-divider-small">
            <p class="product-price">${price_cents} ${price_currency}</p> 
            <hr class="uk-divider-small">
            <p class="product-quantity"><span class="uk-label">quantity</span> ${quantity}</p>
        </div>
        <div class="uk-card-footer">
            <a  href="#" class="uk-button uk-button-text add-to-cart" data-id="${id}">${buttonText}</a>
        </div>
    </div>
</div>
`;

export default productTemplate;
