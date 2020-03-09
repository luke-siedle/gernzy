<?php

namespace Gernzy\Server\GraphQL\Directives;

use \App;
use Closure;
use Gernzy\Server\Services\ExhangeRatesManager;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Cache;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\Directive;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GernzyConvertCurrencyDirective implements Directive, FieldMiddleware
{
    /**
     * Name of the directive as used in the schema.
     *
     * @return string
     */
    public function name(): string
    {
        return 'gernzyConvertCurrency';
    }

    /**
     * Wrap around the final field resolver.
     *
     * @param \Nuwave\Lighthouse\Schema\Values\FieldValue $fieldValue
     * @param \Closure $next
     * @return \Nuwave\Lighthouse\Schema\Values\FieldValue
     */
    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {
        // Retrieve the existing resolver function
        /** @var Closure $previousResolver */
        $previousResolver = $fieldValue->getResolver();

        // Wrap around the resolver
        $wrappedResolver = function ($root, array $args, GraphQLContext $context, ResolveInfo $info) use ($previousResolver) {
            // Call the resolver, passing along the resolver arguments
            /** @var string $result */
            $result = $previousResolver($root, $args, $context, $info);

            if (!isset($context->request->session)) {
                return $result;
            }

            $session = $context->request->session;

            if (!isset($session['data']['currency'])) {
                return $result;
            }

            if (!isset($session->token)) {
                $token = null;
            } else {
                $token = $session->token;
            }

            // sessionCurrency is set through graphql session mutator field
            $sessionCurrency = $session['data']['currency'];

            return (App::make(ExhangeRatesManager::class))
                ->setPrices($result)
                ->setTargetCurrency($sessionCurrency)
                ->setToken($token)
                ->setCachedRate(Cache::get($token, null))
                ->setRpository((new Cache()))
                ->convertPrices();
        };

        // Place the wrapped resolver back upon the FieldValue
        // It is not resolved right now - we just prepare it
        $fieldValue->setResolver($wrappedResolver);

        // Keep the middleware chain going
        return $next($fieldValue);
    }
}
