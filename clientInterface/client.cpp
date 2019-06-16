/********************************************************** Begin MEMO ***********************************************************

g++ -static-libgcc -static-libstdc++ -mwindows -o LxCarsClient.exe client.cpp && LxCarsClient.exe lxcars://copartskba___0603___012OJRO___MOL-LX101___WV1ZZZ70Z2H071589X___01.03.2001___AUF___110000___23376___Ronny%20Zimmermann%20yxz___Bahnhofstr.%2023___15345___Rehfelde___7___debug
g++ -static-libgcc -static-libstdc++ -mwindows -o LxCarsClient.exe client.cpp && LxCarsClient.exe lxcars://copartsnumber___W712___debug
%appdata%\DVSE GmbH\COPARTS Online
HKEY_LOCAL_MACHINE\SOFTWARE\DVSE GmbH\CatClient\Systemname 3 \Control
https://support.shotgunsoftware.com/hc/en-us/articles/219031308-Launching-applications-using-custom-browser-protocols
<Commands><Command Name="[PKW]"><Args><Arg Name="[KBANR]" Value="0603012OJRO" /><Arg Name = "[KZN]" Value ="MOL-LX101" /><Arg Name = "[VIN]" Value ="WV1ZZZ70Z2H071589X" /><Arg Name = "[EZ]" Value ="01.03.2001" /><Arg Name = "[MCODE]" Value ="AUF" /><Arg Name = "[KMStand]" Value ="110000" /><Arg Name = "[AUFTRAGSNR]" Value ="23376" /><Arg Name = "[KDName]" Value ="Ronny Zimmermann yxz" /><Arg Name = "[STRASSE]" Value ="Bahnhofstr. 23" /><Arg Name = "[PLZ]" Value ="15345" /><Arg Name = "[ORT]" Value ="Rehfelde" /><Arg Name = "[GENARTNR]" Value ="7" /></Args></Command></Commands>

*********************************************************** End MEMO ************************************************************/

#include <iostream>
#include <fstream>
#include <sstream>
#include <string>
#include <algorithm>
#include <conio.h>
#include <windows.h>
#include <tchar.h>
#include <unistd.h>
#include <vector>

#define TOTALBYTES    8192
#define BYTEINCREMENT 4096
#define SIZE           256

using namespace std;
string windowTitleName;

BOOL CALLBACK FindWindowBySubstr( HWND, LPARAM );
void findAndReplaceAll( string&, string, string );
vector<string> explode( string& str, const string& delimiter );

int main( int argc, char* argv[] ){

    DWORD BufferSize = TOTALBYTES;
    string xmloutput;
    bool debug = FALSE;
    const size_t size = SIZE;
    string pathcontrolfile;
    string controlfile;
    string param = argv[1];
    string comdata = param.substr( 9 ); //Command and Data without lxcars://

    findAndReplaceAll( comdata, "%20", " " );
    vector<string> dataarray = explode( comdata, "___" );

    if(  dataarray[0] == "copartskba" && ( dataarray[14] == "debug" || dataarray[14] == "debug/" ) )
        debug = TRUE;
    if(  dataarray[0] == "copartsnumber" && ( dataarray[2] == "debug" || dataarray[2] == "debug/" ) )
        debug = TRUE;

    ofstream debugFile;
    char str[MAX_PATH];
    GetModuleFileNameA( NULL, str, MAX_PATH );
    string path = str;
    path = path.substr( 0, path.find( argv[0] ) );

    if( debug ){
        debugFile.open( path + "debug.txt" );
        debugFile << "Commandline Parameter: " << argv[1] << endl;
        cout << string(50, '\n');
        cout << "!!! ***** LxCars Client Debug Mode ***** !!!" << endl << endl;
    }

    if( dataarray[0] == "copartskba" || dataarray[0] == "copartsnumber" ){
        HKEY hKey = 0;
        PPERF_DATA_BLOCK dwValueTypeControl =  (PPERF_DATA_BLOCK) malloc( BufferSize );
        DWORD dwValueSizeControl = BufferSize;
        PPERF_DATA_BLOCK dwValueTypePath = (PPERF_DATA_BLOCK) malloc( BufferSize );
        DWORD dwValueSizePath = BufferSize;
        PPERF_DATA_BLOCK dwValueTypeAppName = (PPERF_DATA_BLOCK) malloc( BufferSize );;
        DWORD  dwValueSizeAppName = BufferSize;

        LPCTSTR strKeyRegPath = TEXT( "SOFTWARE\\DVSE GmbH\\CatClient\\COPARTS Online" );
        LPCTSTR strKeyControl = TEXT( "Control" );
        LPCTSTR strKeyPath = TEXT( "Path" );
        LPCTSTR strKeyApp = TEXT( "AppName" );

        byte byteValueControl[size];
        byte byteValuePath[size];
        byte byteValueAppName[size];
        TCHAR cOutputPath[size];

        // for window in foreground
        const TCHAR SUBSTRING[] = TEXT("COPARTS Online");

        // read registry
        if( RegOpenKeyExA( HKEY_LOCAL_MACHINE,  strKeyRegPath  , 0, KEY_ALL_ACCESS, &hKey ) != ERROR_SUCCESS ){
            cout << "ERROR - Unable to open the key! " << ERROR_SUCCESS << endl;
        }

        if( RegQueryValueExA( hKey, strKeyControl , NULL, NULL,  (LPBYTE) byteValueControl, &dwValueSizeControl ) != ERROR_SUCCESS ){
            cout << "ERROR - Unable to get the key's byteValueControl! " << ERROR_SUCCESS << endl;
        }

        if( RegQueryValueExA( hKey,  strKeyPath , NULL, NULL,  (LPBYTE) byteValuePath, &dwValueSizePath ) != ERROR_SUCCESS ){
            cout << "ERROR - Unable to get the key's byteValuePath! " <<  ERROR_SUCCESS <<endl;
        }

        if( RegQueryValueExA( hKey, strKeyApp, NULL, NULL,  (LPBYTE) byteValueAppName, &dwValueSizeAppName ) != ERROR_SUCCESS ){
            cout << "ERROR - Unable to get the key's value byteValueAppName!";
        }

        RegCloseKey( hKey );

        //expand special folder %appadata%
        ExpandEnvironmentStrings( ( LPSTR )&byteValueControl, ( LPSTR )&cOutputPath,  size );

        //convert to string
        pathcontrolfile = reinterpret_cast< char const* >( cOutputPath );

        ofstream outfile;
        controlfile = pathcontrolfile + "Controlfile.cf";
        outfile.open( controlfile );
        if( dataarray[0] == "copartskba" )
            xmloutput = "<Commands><Command Name=\"[PKW]\"><Args><Arg Name=\"[KBANR]\" Value=\"" + dataarray[1] + dataarray[2] + "\" /><Arg Name = \"[KZN]\" Value =\"" + dataarray[3] + "\" /><Arg Name = \"[VIN]\" Value =\"" + dataarray[4] + "\" /><Arg Name = \"[EZ]\" Value =\"" + dataarray[5] + "\" /><Arg Name = \"[MCODE]\" Value =\"" + dataarray[6] + "\" /><Arg Name = \"[KMStand]\" Value =\"" + dataarray[7] + "\" /><Arg Name = \"[AUFTRAGSNR]\" Value =\"" + dataarray[8]  + "\" /><Arg Name = \"[KDName]\" Value =\"" + dataarray[9] + "\" /><Arg Name = \"[STRASSE]\" Value =\"" + dataarray[10]  + "\" /><Arg Name = \"[PLZ]\" Value =\"" + dataarray[11]  + "\" /><Arg Name = \"[ORT]\" Value =\"" + dataarray[12]  + "\" /><Arg Name = \"[GENARTNR]\" Value =\"" + dataarray[13] + "\" /></Args></Command></Commands>";
        if( dataarray[0] == "copartsnumber" )
            xmloutput = "<Commands><Command Name=\"[ARTIKEL]\"><Args><Arg Name =\"[ARTIKELNR]\" Value =\"" + dataarray[1] + "\" /></Args></Command></Commands>";
        //<Command Name="[ARTIKEL]"><Arg Name = „[ARTIKELNR]“ Value ="" />
        outfile << xmloutput << endl;
        outfile.close();

        Sleep( 150 );

        // for window in foreground
        EnumWindows( FindWindowBySubstr, ( LPARAM )SUBSTRING );
        HWND windowHandle = FindWindow( NULL, windowTitleName.c_str() );
        if( windowTitleName.length() ){
            ShowWindow( windowHandle, SW_SHOWMAXIMIZED );
            SetForegroundWindow( windowHandle );
        }
        else{
            stringstream coparts;
            coparts << "\"" << byteValuePath << "\\" << byteValueAppName << "\"";
            if( debug ) cout << "Starte Coparts: ";// << coparts << endl;
            WinExec( coparts.str().c_str(), 1 );
        }



    }

    if( debug ){
        debugFile << "Path: " << path << endl << endl;
        debugFile << "Commandline Parameter EINS: " << argv[1] << endl << endl;
        debugFile << "Controlfile: " << controlfile << endl << endl;
        debugFile << "XML-Output: " << xmloutput << endl;
        debugFile.close();

        cout << "Path: " << path << endl << endl;
        cout << "Commandline Parameter EINS: " << argv[1] << endl << endl;
        cout << "Controlfile: " << controlfile << endl << endl;
        cout << "XML-Output: " << xmloutput << endl << endl << endl;
        cout << "Press any key to close..." << endl;
        getche();
    }

    return 0;
}

BOOL CALLBACK FindWindowBySubstr( HWND hwnd, LPARAM substring ){
    const DWORD TITLE_SIZE = 1024;
    TCHAR windowTitle[TITLE_SIZE];

    if( GetWindowText( hwnd, windowTitle, TITLE_SIZE ) ){
        //cout << "windowTitle: " << windowTitle << endl;
        // Uncomment to print all windows being enumerated
        if( _tcsstr( windowTitle, LPCTSTR( substring ) ) != NULL ){
            //cout << windowTitle << endl;
           // We found the window! Stop enumerating.
           windowTitleName = windowTitle;
           return false;
        }
    }
    return true;
}

void findAndReplaceAll( string& data, string toSearch, string replaceStr ){
    // Get the first occurrence
    size_t pos = data.find( toSearch );
    // Repeat till end is reached
    while( pos != string::npos ){
        // Replace this occurrence of Sub String
        data.replace( pos, toSearch.size(), replaceStr );
        // Get the next occurrence from the current position
        pos = data.find( toSearch, pos + toSearch.size() );
    }
}

vector<string> explode( string& str, const string& delimiter ){
    vector<string> result;
    size_t pos = 0;
    string token;
    while( ( pos = str.find( delimiter ) ) != string::npos ){
        token = str.substr( 0, pos );
        result.push_back( token );
        str.erase( 0, pos + delimiter.length() );
    }
    result.push_back( str ); //last part
    return result;
}