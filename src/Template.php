<?php

namespace EternalNerd\ConfigDude;

class Template
{
    /**
     * Returns HTML in Twig format to render Boolean as a checkbox.
     * 
     * @return string 
     */
    static function checkBox() :string
    {
        return <<<EOF
        <fieldset>
            <label for="{{vars.name}}">
                <input type="checkbox" name="{{vars.name}}" id="{{vars.name}}" {% if vars.defaultValue == "checked" %}checked{% endif %}>
            </label>
        </fieldset>
        EOF.PHP_EOL;
    }

    /**
     * Returns HTML in Twig format to render Integer as a text input with min/max length.
     * 
     * @return string 
     */
    static function inputInteger() :string
    {
        return <<<EOF
        <label for="{{vars.name}}">{{vars.prettyName}}
            <input type="text" name="{{vars.name}}" id="{{vars.name}}" value="{{vars.defaultValue}}" min="{{vars.min}}" max="{{vars.max}}">
        </label>
        EOF.PHP_EOL;
    }

    /**
     * Returns HTML in Twig format to render String as a text input with min/max length.
     * 
     * @return string 
     */
    static function inputString() :string
    {
        return <<<EOF
        <label for="{{vars.name}}">{{vars.prettyName}}
            <input type="text" name="{{vars.name}}" id="{{vars.name}}" value="{{vars.defaultValue}}" minLength="{{vars.min}}" maxLength="{{vars.max}}">
        </label>
        EOF.PHP_EOL;
    }    
    /**
     * Returns HTML in Twig format to render a range selection with min/max values.
     * 
     * @return string
     */
    static function range() :string
    {
        return <<<EOF
        <label for="{{vars.name}}">{{vars.prettyName}} - Min: {{vars.min}} | Max: {{vars.max}}
            <input type="range" name="{{vars.name}}" id="{{vars.name}}" value="{{vars.defaultValue}}" min="{{vars.min}}" max="{{vars.max}}" oninput="{{vars.name}}Num.value = this.value">
            <output name="{{vars.name}}Num">{{vars.defaultValue}}</output>
        </label>        
        EOF.PHP_EOL;
    }

    /**
     * Returns HTML in Twig format to render an end of a section.
     * 
     * @return string 
     */
    static function sectoinEnd() :string
    {
        return <<<EOF
        </section>
        EOF.PHP_EOL;
    }

    /**
     * Returns HTML in Twig format to render the start of a section.
     * Will not display <h3> if this is an anonymous section.
     * @return string 
     */    
    static function sectionStart() :string
    {
        return <<<EOF
        {% if 'anonymous' in vars.prettyName %}
        <section>
        {% else %}
        <section>
            <h3>{{vars.prettyName}}</h3>
        {% endif %}
        EOF.PHP_EOL;
    }

    /**
     * Returns HTML in Twig format to render a textArea.
     * 
     * @return string 
     */    
    static function textArea() :string
    {
        return <<<EOF
        <label for="{{vars.name}}">
            <textarea name="{{vars.name}}" id="{{vars.name}}">
                {{vars.defaultValue}}
            </textarea>
        </label>
        EOF.PHP_EOL;
    }
}