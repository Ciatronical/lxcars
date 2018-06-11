#include <iostream>
#include <fstream>
#include <string>
#include <conio.h>
#include <windows.h>
using namespace std;

// g++ -o LxCarsClient.exe client.cpp && LxCarsClient.exe lxcars://kba0710362020945___SRB-DT11___Tina%20Kuzia
// %appdata%\DVSE GmbH\COPARTS Online
// HKEY_LOCAL_MACHINE\SOFTWARE\DVSE GmbH\CatClient\Systemname 3 \Control
// https://support.shotgunsoftware.com/hc/en-us/articles/219031308-Launching-applications-using-custom-browser-protocols

int main(int argc, char* argv[]){
	
	string param = argv[1];
	string comdata = param.substr( 9 ); //Command and Data
	comdata = comdata.substr( 0, comdata.size() - 1 );
    string command = comdata.substr( 0, 3 );
    string data = comdata.substr( 3 );
	
	HKEY hKey;
	string strKeyPath = "SOFTWARE\\DVSE GmbH\\CatClient\\COPARTS Online";
	string strKeyName = "Control";
	DWORD dwValueType;
	const size_t size = 256;
	byte byteValue[size];
	DWORD dwValueSize;
	TCHAR cOutputPath[size]; 
	
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
	}	
		
	return 0;
}