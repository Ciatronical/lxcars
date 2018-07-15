#include <iostream>
#include <windows.h>
#define TOTALBYTES    8192
#define BYTEINCREMENT 4096
#define SIZE           256
using namespace std;

//g++ -o regTest.exe regTest.cpp && regTest.exe
int main(){
	size_t size = SIZE;
	HKEY hKey = 0;
	DWORD BufferSize = TOTALBYTES;
	
	PPERF_DATA_BLOCK dwValueTypeControl =  (PPERF_DATA_BLOCK) malloc( BufferSize );
	DWORD dwValueSizeControl = BufferSize;
	
	PPERF_DATA_BLOCK dwValueTypePath = (PPERF_DATA_BLOCK) malloc( BufferSize );
	DWORD dwValueSizePath = BufferSize;
	
	PPERF_DATA_BLOCK dwValueTypeAppName = (PPERF_DATA_BLOCK) malloc( BufferSize );;
	DWORD  dwValueSizeAppName = BufferSize;
	
	LPCTSTR strKeyRegPath = TEXT( "SOFTWARE\\DVSE GmbH\\CatClient\\COPARTS Online\\" );
	LPCTSTR strKeyControl = TEXT( "Control" );
	LPCTSTR strKeyPath = TEXT( "Path" );
	LPCTSTR strKeyApp = TEXT( "AppName" );
	
	byte byteValueControl[size];
	byte byteValuePath[size];
	byte byteValueAppName[size];
	
	// read registry
	if( RegOpenKeyExA( HKEY_LOCAL_MACHINE,  strKeyRegPath  , 0, KEY_ALL_ACCESS, &hKey ) != ERROR_SUCCESS ){
		cout << "ERROR - Unable to open the key! " << ERROR_SUCCESS << endl;	
	}
	else{
		cout << "Success! Key is open." << endl;
	}

	if( RegQueryValueExA( hKey, strKeyControl , NULL, NULL,  (LPBYTE) byteValueControl, &dwValueSizeControl ) != ERROR_SUCCESS ){
		cout << "ERROR - Unable to get the key's byteValueControl! " << ERROR_SUCCESS << endl;
	}
	else{
		cout << "Success! byteValueControl is: " << byteValueControl << endl;
	}
	
	if( RegQueryValueExA( hKey,  strKeyPath , NULL, NULL,  (LPBYTE) byteValuePath, &dwValueSizePath ) != ERROR_SUCCESS ){
		cout << "ERROR - Unable to get the key's byteValuePath! " <<  ERROR_SUCCESS <<endl;
	}
	
	else{
		cout << "Success! byteValuePath is: " << byteValuePath << endl;
	}
	
	if( RegQueryValueExA( hKey, strKeyApp, NULL, NULL,  (LPBYTE) byteValueAppName, &dwValueSizeAppName ) != ERROR_SUCCESS ){
		cout << "ERROR - Unable to get the key's value byteValueAppName!";
	}
	else{
		cout << "Success! byteValuePath is: " << byteValueAppName << endl;
	}
	
	RegCloseKey( hKey );
}