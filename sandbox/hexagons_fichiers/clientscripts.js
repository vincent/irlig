/* GDNet Client Scripts
 * Useful functions that do... things.
 * Richard "Superpig" Fine, 20080321
 * Relies on the Prototype JS library, http://www.prototypejs.org/
 ********************************************************************/
 
function autoLinkify(targetElementCssSelector)
{
    var posts = $$(targetElementCssSelector);
    for(var postIndex = 0; postIndex < posts.length; ++postIndex)
    {
        posts[postIndex].innerHTML = posts[postIndex].innerHTML.replace(/([^"]|^)((ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?)/g, '$1<a href="$2">$2</a>');
    }
}

function revealExternalUrls(targetElementCssSelector)
{
    var links = $$(targetElementCssSelector + ' a');
    for(var linkIndex = 0; linkIndex < links.length; ++linkIndex)
    {
        if(links[linkIndex].href)
        {
            var domain = links[linkIndex].href.match(/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?([^ \t\r\n\v\f\/]+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/)
            if(domain[3] == "www.gamedev.net") continue;
            links[linkIndex].insert({after:" [" + domain[3] + "]"});
        }
    }
}