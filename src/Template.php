<?php

namespace EternalNerd\ConfigDude;

class Template
{
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

    static function inputInteger() :string
    {
        return <<<EOF
        <label for="{{vars.name}}">{{vars.prettyName}}
            <input type="text" name="{{vars.name}}" id="{{vars.name}}" value="{{vars.defaultValue}}" min="{{vars.min}}" max="{{vars.max}}">
        </label>
        EOF.PHP_EOL;
    }

    static function inputString() :string
    {
        return <<<EOF
        <label for="{{vars.name}}">{{vars.prettyName}}
            <input type="text" name="{{vars.name}}" id="{{vars.name}}" value="{{vars.defaultValue}}" minLength="{{vars.min}}" maxLength="{{vars.max}}">
        </label>
        EOF.PHP_EOL;
    }    

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
    
    static function sectoinEnd() :string
    {
        return <<<EOF
        </section>
        EOF.PHP_EOL;
    }

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
}