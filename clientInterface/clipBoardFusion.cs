using System;
using System.Collections.Generic;
// STRG + LEER
public static class ClipboardFusionHelper{
    public static string ProcessText( string text ){
        text = text.Replace( " ", "" );
        BFS.Clipboard.PasteText( text );
        return text;
    }
}