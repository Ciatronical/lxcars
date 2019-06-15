#include <iostream>
#include <vector>
#include <string>
using namespace std;

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

int main(){
    string test = "EINS___ZWEI___DREI____V_IER_";
    cout << test << endl;
    vector<string> dataarray = explode( test, "___" );

    for( vector<int>::size_type i = 0; i != dataarray.size(); i++) {
        cout << dataarray[i] << endl;
    }
    return 0;
}