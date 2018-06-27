#include <iostream>
#include <fstream>
#include <string>
#include <conio.h>
#include <windows.h>
//#include <stdlib.h>
#include <tchar.h>
using namespace std;
string windowClassName;

// g++ -o LxCarsClient.exe client.cpp && LxCarsClient.exe lxcars://kba0710362020945___SRB-DT11___Tina%20Kuzia
// %appdata%\DVSE GmbH\COPARTS Online
// HKEY_LOCAL_MACHINE\SOFTWARE\DVSE GmbH\CatClient\Systemname 3 \Control
// https://support.shotgunsoftware.com/hc/en-us/articles/219031308-Launching-applications-using-custom-browser-protocols

BOOL CALLBACK FindWindowClassBySubstr( HWND, LPARAM );


int main(int argc, char* argv[]){
	
	string param = argv[1];
	string comdata = param.substr( 9 ); //Command and Data
	comdata = comdata.substr( 0, comdata.size() - 1 );
    string command = comdata.substr( 0, 3 );
    string data = comdata.substr( 3 );
	
	// for registry
	HKEY hKey;
	string strKeyPath = "SOFTWARE\\DVSE GmbH\\CatClient\\COPARTS Online";
	string strKeyName = "Control";
	DWORD dwValueType;
	const size_t size = 256;
	byte byteValue[size];
	DWORD dwValueSize;
	TCHAR cOutputPath[size]; 
	
	// for window in foreground
	const TCHAR SUBSTRING[] = TEXT("WindowsForms10.Window.8.app");
	//const string SUBSTRING = "WindowsForms10.Window.8.app";
	
	// read registry
	if( RegOpenKeyExA( HKEY_LOCAL_MACHINE, strKeyPath.c_str(), 0, KEY_ALL_ACCESS, &hKey ) != ERROR_SUCCESS ){
		cout << "ERROR - Unable to open the key!";
		_getch();
		return -1;
	}
	if( RegQueryValueExA( hKey, strKeyName.c_str(), NULL, &dwValueType, byteValue, &dwValueSize ) != ERROR_SUCCESS ){
		cout << "ERROR - Unable to get the key's value!";
		_getch();
		return -1;
	}
	RegCloseKey( hKey );
	
	//expand special folder %appadata%
	ExpandEnvironmentStrings( ( LPSTR )&byteValue, ( LPSTR )&cOutputPath,  size );
	
	//convert to string
	string path( ( reinterpret_cast< char const* >( cOutputPath ) ) );
	
	if( command == "kba" ){
		//splitt data to plate and name
		string kbadata = data.substr( 0, data.find( "___" ) );
		string tmp = data.substr( data.find( "___" ) + 3 );
		string plate = tmp.substr( 0, tmp.find( "___" ) );
		string name  = tmp.substr( tmp.find( "___" ) + 3 );
		name = name.replace( name.find( "%20" ), 3, " " );
		
		ofstream outfile;
		/*outfile.open( path + "\\Controlfile.cf" );
		outfile << "<Commands> <Command Name=\"[ APP]\"> <Arg Name=\"[COMMAND]\" Value=\"max\" /> </Args></Command></Commands>" << endl;
		outfile.close();
		Sleep( 200 );*/
		outfile.open( path + "\\Controlfile.cf" );
		outfile << "<Commands>  <Command Name=\"[PKW]\"> <Args> <Arg Name=\"[KBANR]\" Value=\"" << kbadata << "\" /> <Arg Name = \"[KZN]\" Value =\"" << plate << "\" /> <Arg Name = \"[KDName]\" Value =\"" << name << "\" /> </Args></Command></Commands>" << endl;
		outfile.close();	

		// for window in foreground
		HWND windowHandle;
		EnumWindows( FindWindowClassBySubstr, ( LPARAM )SUBSTRING );
		windowHandle = FindWindow( windowClassName.c_str(), 0 );
		SetForegroundWindow( windowHandle );

	}	
		
	return 0;
}

BOOL CALLBACK FindWindowClassBySubstr( HWND hwnd, LPARAM substring ){
    const DWORD WINDOW_CLASS_SIZE = 1024;
    TCHAR windowClass[WINDOW_CLASS_SIZE];

    if( GetClassName( hwnd, windowClass, WINDOW_CLASS_SIZE ) ){
        if( _tcsstr( windowClass, LPCTSTR( substring ) ) != NULL ){ //finish
            windowClassName = windowClass;
			return false;
        }
    }
    return true; // Need to continue enumerating windows
}
