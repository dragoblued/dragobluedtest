<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XssSanitizer
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function(&$input) {
            if (is_string($input)) {
                $input = strip_tags($input, [
                    "address", "article", "aside", "footer", "header", "h1", "h2", "h3", "h4",
                    "h5", "h6", "hgroup", "main", "nav", "section", "blockquote", "dd", "div",
                    "dl", "dt", "figcaption", "figure", "hr", "li", "main", "ol", "p", "pre",
                    "ul", "a", "abbr", "b", "bdi", "bdo", "br", "cite", "code", "data", "dfn",
                    "em", "i", "kbd", "mark", "q", "rb", "rp", "rt", "rtc", "ruby", "s", "samp",
                    "small", "span", "strong", "sub", "sup", "time", "u", "var", "wbr", "caption",
                    "col", "colgroup", "table", "tbody", "td", "tfoot", "th", "thead", "tr"
                ]);
            }
        });
        $request->merge($input);
        return $next($request);
    }
}
