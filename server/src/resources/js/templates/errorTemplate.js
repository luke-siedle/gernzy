const errorTemplate = message => `
<div class="uk-alert-danger" uk-alert>
    <a class="uk-alert-close" uk-close></a>
    <p>${message}</p>
</div>
`;

export default errorTemplate;
