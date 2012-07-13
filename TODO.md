* Create a UrlFactory class (mimic the other Factory classes in other
  packages, e.g. Aura\Http\Header\Factory). Its newInstance() method
  should take a string, parse it, create the Url instance, and then
  feed the instance with the parsed values.
* Add a method UrlFactory::newCurrent() (or something like that) to
  returns an instance of the current request URL. This means the
  factory constructor probably needs a $_SERVER param fed into it
  (do not use $_SERVER directly -- cf. Aura\Router\Map::match()).
* Write unit tests
* Per Bahtiar Gadimov, we may eventually need an additional Parser
  class, but at this point that may be overkill.
  <https://bugs.php.net/bug.php?id=52923>