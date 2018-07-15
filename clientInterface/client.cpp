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

#define TOTALBYTES    8192
#define BYTEINCREMENT 4096
#define SIZE           256

using namespace std;
string windowTitleName;

// g++ -o LxCarsClient.exe client.cpp && LxCarsClient.exe lxcars://kba0710362020945___SRB-DT11___Tina%20Kuzia
// %appdata%\DVSE GmbH\COPARTS Online
// HKEY_LOCAL_MACHINE\SOFTWARE\DVSE GmbH\CatClient\Systemname 3 \Control
// https://support.shotgunsoftware.com/hc/en-us/articles/219031308-Launching-applications-using-custom-browser-protocols

BOOL CALLBACK FindWindowBySubstr( HWND, LPARAM );
void findAndReplaceAll( string&, string, string );

int main(int argc, char* argv[]){
	
	DWORD BufferSize = TOTALBYTES;
	bool debug = FALSE;
	const size_t size = SIZE;
	string param = argv[1];
	string comdata = param.substr( 9 ); //Command and Data
	comdata = comdata.substr( 0, comdata.size() - 1 );
    string command = comdata.substr( 0, 3 );
    string data = comdata.substr( 3 );
			
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
		
	if( command == "kba" ){
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
		
		//splitt data to plate and name
		string kbadata = data.substr( 0, data.find( "___" ) );
		string tmp = data.substr( data.find( "___" ) + 3 );
		string plate = tmp.substr( 0, tmp.find( "___" ) );
		string name  = tmp.substr( tmp.find( "___" ) + 3 );
		//name = name.replace( name.find( "%20" ), 3, " " );
		findAndReplaceAll( name, "%20", " " );
		
		ofstream outfile;
		outfile.open( path + "\\Controlfile.cf" );
		outfile << "<Commands>  <Command Name=\"[PKW]\"> <Args> <Arg Name=\"[KBANR]\" Value=\"" << kbadata << "\" /> <Arg Name = \"[KZN]\" Value =\"" << plate << "\" /> <Arg Name = \"[KDName]\" Value =\"" << name << "\" /> </Args></Command></Commands>" << endl;
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
		pos =data.find( toSearch, pos + toSearch.size() );
	}
}
