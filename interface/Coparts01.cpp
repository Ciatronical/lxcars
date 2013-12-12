#include <windows.h>
#include <stdio.h>
 
int main(int argc, char **argv)
{
    unsigned char temp[99] = {""};  
    unsigned long size = sizeof(temp);
    HKEY hKey;
    
    RegOpenKey(HKEY_CURRENT_USER, "Software\\Microsoft\\MediaPlayer\\Setup\\CreatedLinks", &hKey);
    RegQueryValueEx(hKey, "AppName", NULL, NULL, temp, &size);
    RegCloseKey(hKey);
    printf("Value: %s (Size: %lu)\n", temp, size);
    return 0;
}