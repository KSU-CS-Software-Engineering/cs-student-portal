# Contribution guide

This guide specifies code style requirements for this project.

- All files must use 4 spaces (0x20 ASCII character) for indentation, except where specified otherwise.

- All files must end with an empty line.

- No file should contain line longer than 120 characters, except where absolutely necessary.

- Properties and variables must have `camelCase` formatted names

- Constants must have `UPPERCASE_UNDERSCORE` formatted names

- All **PHP** files must follow the following rules:

    - [PSR-1][psr-1] and [PSR-2][psr-2] standards (except Blade files)

    - Strings are enclosed in single quotes if no escape sequences are present and no variable parsing is necessary, otherwise double quotes are used
    
    - To create an array, square brackets (`[]`) are used instead of `array()` function
    
    - If array items are on separate lines, there is a comma after the last item

- All **JavaScript** files must follow the following rules:

    - Use [ECMAScript 2015 (aka ES6)][es2015] syntax

    - Follow [PSR-1][psr-1] and [PSR-2][psr-2] standards where applicable and not stated otherwise (indentation, spaces, naming conventions, …)

    - Curly brackets are the the same line as the definition of classes, methods and functions

    - All statements are terminated by a semicolon

    - Strings are enclosed in double quotes

    - Use [template strings][tmpl-str] (aka template literals) for printing a variable instead of concatenation of strings
    
    - There is a comma after last item in list (array, object) when multiline

    - See the code example below:
        ```javascript
        import MyClass from "./path/to/MyClass";
        
        class MyApp {
        
            constructor(fooBar) {
                this.FOO_BAR = new MyClass();
                this.fooBar = fooBar;
            }
        
            doSomething() {
                if (this.fooBar === false) {
                    return "NOTHING";
                } else {
                    return "SOMETHING";
                }
            }
        }
        
        function exampleFunction() {
            const PARAM = "false";
            let myApp = new MyApp(PARAM);
            myApp.doSomething();
        
            let exampleCar = {
                make: "Awesome manufacturer",
                model: "The best one",
                year: 2019,
                features: [
                    "5-speed manual transmission",
                    "Cruise control",
                    "Four-wheel drive",
                    "Theft protection",
                ],
            };
        
            for (let i = 0; i < 5; i++) {
                console.log(`template 'string' example: "${PARAM}"`);
            }
        }
        ```

[psr-1]: https://www.php-fig.org/psr/psr-1/ "PSR-1"
[psr-2]: https://www.php-fig.org/psr/psr-2/ "PSR-2"
[es2015]: http://www.ecma-international.org/ecma-262/6.0/index.html "ECMAScript 2015 Language Specification"
[tmpl-str]: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals "Template Strings – MDN"
