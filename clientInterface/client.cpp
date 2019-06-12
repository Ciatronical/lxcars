#include <iostream>
#include <fstream>
#include <sstream>
#include <string>
#include <algorithm>
#include <conio.h>
#include <windows.h>
//#include <stdlib.h>
#include <tchar.h>
#include <unistd.h>
#include <vector>
//#include <iterator>

#define TOTALBYTES    8192
#define BYTEINCREMENT 4096
#define SIZE           256

using namespace std;
string windowTitleName;

// g++ -static-libgcc -static-libstdc++  -o LxCarsClient.exe client.cpp && LxCarsClient.exe lxcars://coparts___0603___012OJRO___MOL-LX101___WV1ZZZ70Z2H071589X___110000___23376___Ronny%20Zimmermann%20yxz___Bahnhofstr.%2023___15345___Rehfelde
// %appdata%\DVSE GmbH\COPARTS Online
// HKEY_LOCAL_MACHINE\SOFTWARE\DVSE GmbH\CatClient\Systemname 3 \Control
// https://support.shotgunsoftware.com/hc/en-us/articles/219031308-Launching-applications-using-custom-browser-protocols
// <Commands>  <Command Name="[PKW]"> <Args> <Arg Name="[KBANR]" Value="0603012OJRO" /> <Arg Name = "[KZN]" Value ="MOL-LX101" /> <Arg Name = "[VIN]" Value ="WV1ZZZ70Z2H071589X" /> <Arg Name = "[KMStand]" Value ="100000" /> <Arg Name = "[AUFTRAGSNR]" Value ="23376" /> <Arg Name = "[KDName]" Value ="Ronny Zimmermann yxz" /> <Arg Name = "[STRASSE]" Value ="Bahnhofstr. 23" /> <Arg Name = "[PLZ]" Value ="15345" /> <Arg Name = "[ORT]" Value ="Rehfelde" /> </Args></Command></Commands>


BOOL CALLBACK FindWindowBySubstr( HWND, LPARAM );
void findAndReplaceAll( string&, string, string );
vector<string> explode( const string& str, const char& ch );

int main(int argc, char* argv[]){

    DWORD BufferSize = TOTALBYTES;
    bool debug = FALSE; //todo
    const size_t size = SIZE;
    string param = argv[1];
    string comdata = param.substr( 9 ); //Command and Data without lxcars://

    findAndReplaceAll( comdata, "%20", " " );
    std::vector<std::string> dataarray = explode( comdata, '_' );

    if( debug ){
        ofstream debugFile;
        char str[MAX_PATH];
        GetModuleFileNameA( NULL, str, MAX_PATH );
        string path = str;
        path = path.substr( 0, path.find( argv[0] ) );
        debugFile.open( path + "debug.txt" );
        debugFile << argv[1] << endl;
        debugFile.close();
    }

    if( dataarray[0] == "coparts" ){
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
        string path( ( reinterpret_cast< char const* >( cOutputPath ) ) );


        ofstream outfile;
        outfile.open( path + "\\Controlfile.cf" );
        outfile << "<Commands>  <Command Name=\"[PKW]\"> <Args> <Arg Name=\"[KBANR]\" Value=\"" << dataarray[1] << dataarray[2] << "\" /> <Arg Name = \"[KZN]\" Value =\"" << dataarray[3] << "\" /> <Arg Name = \"[VIN]\" Value =\"" << dataarray[4] << "\" /> <Arg Name = \"[KMStand]\" Value =\"" << dataarray[5] << "\" /> <Arg Name = \"[AUFTRAGSNR]\" Value =\"" << dataarray[6]  << "\" /> <Arg Name = \"[KDName]\" Value =\"" << dataarray[7] << "\" /> <Arg Name = \"[STRASSE]\" Value =\"" << dataarray[8]  << "\" /> <Arg Name = \"[PLZ]\" Value =\"" << dataarray[9]  << "\" /> <Arg Name = \"[ORT]\" Value =\"" << dataarray[10]  << "\" /> </Args></Command></Commands>" << endl;
        outfile.close();
        Sleep( 100 );

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
            WinExec( coparts.str().c_str(), 1 );
        }

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

vector<string> explode( const string& str, const char& ch ){
    string next;
    vector<string> result;

    // For each character in the string
    for( string::const_iterator it = str.begin(); it != str.end(); it++ ){
        // If we've hit the terminal character
        if( *it == ch ){
            // If we have some characters accumulated
            if( !next.empty() ){
                // Add them to the result vector
                result.push_back( next );
                next.clear();
            }
        }
        else{
            // Accumulate the next character into the sequence
            next += *it;
        }
    }
    if( !next.empty() )
        result.push_back( next );
    return result;
}
