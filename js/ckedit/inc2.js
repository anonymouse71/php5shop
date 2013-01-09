//<![CDATA[

CKEDITOR.replace( 'editor2',
{
    /*
     * Style sheet for the contents
     */
    contentsCss : 'body {color:#000; background-color#:FFF;}',

    /*
     * Simple HTML5 doctype
     */
    docType : '<!DOCTYPE HTML>',

    /*
     * Core styles.
     */
    coreStyles_bold	: {
        element : 'b'
    },
    coreStyles_italic	: {
        element : 'i'
    },
    coreStyles_underline	: {
        element : 'u'
    },
    coreStyles_strike	: {
        element : 'strike'
    },

    /*
     * Font face
     */
    // Define the way font elements will be applied to the document. The "font"
    // element will be used.
    font_style :
    {
        element		: 'font',
        attributes		: {
            'face' : '#(family)'
        }
    },

    /*
     * Font sizes.
     */
    fontSize_sizes : 'xx-small/1;x-small/2;small/3;medium/4;large/5;x-large/6;xx-large/7',
    fontSize_style :
    {
        element		: 'font',
        attributes	: {
            'size' : '#(size)'
        }
    } ,

 /*
  * Font colors.
  */
    colorButton_enableMore : true,

    colorButton_foreStyle :
    {
        element : 'font',
        attributes : {
            'color' : '#(color)'
        },
        overrides	: [ {
            element : 'span',
            attributes : {
                'class' : /^FontColor(?:1|2|3)$/
            }
        } ]
},

colorButton_backStyle :
{
    element : 'font',
    styles	: {
        'background-color' : '#(color)'
    }
},
/*
 * Styles combo.
 */
stylesSet :
[
{
    name : 'Computer Code',
    element : 'code'
},
{
    name : 'Keyboard Phrase',
    element : 'kbd'
},
{
    name : 'Sample Text',
    element : 'samp'
},
{
    name : 'Variable',
    element : 'var'
},

{
    name : 'Deleted Text',
    element : 'del'
},
{
    name : 'Inserted Text',
    element : 'ins'
},

{
    name : 'Cited Work',
    element : 'cite'
},
{
    name : 'Inline Quotation',
    element : 'q'
}
],

on : {
    'instanceReady' : configureHtmlOutput
}
});

//]]>