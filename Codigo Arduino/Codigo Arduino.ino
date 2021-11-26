#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include "hw_timer.h"

const char *ssid = "";
const char *password = "";
char SERVER[50] = ""; 
int SERVERPORT = 0;
String USERNAME = "";   
char PASSWORD[50] = "";     
char PLACA[50];
char INSTRUCCION[50];
int Foco = 16;  //D0        
const byte zcPin = 12; //D6
const byte pwmPin = 13;  //D7
byte fade = 1;
byte state = 1;
byte tarBrightness = 255;
byte curBrightness = 0;
byte zcState = 0;

WiFiClient espClient;
PubSubClient client(espClient);
void setup() {
    pinMode(Foco, OUTPUT); // D0 (Foco ON/OFF)
    digitalWrite(Foco, LOW);
    Serial.begin(115200);   
    pinMode(zcPin, INPUT_PULLUP);
    pinMode(pwmPin, OUTPUT);
    attachInterrupt(zcPin, zcDetectISR, RISING); 
    hw_timer_init(NMI_SOURCE, 0);
    hw_timer_set_func(dimTimerISR);
    WiFi.begin(ssid, password);
    while(WiFi.status() != WL_CONNECTED){ 
        delay(500);
        Serial.print(".");
    }
    Serial.println("");
    Serial.println("WiFi conectado");
    Serial.println(WiFi.localIP());
    client.setServer(SERVER, SERVERPORT);
    client.setCallback(callback);    
    String instruccion = "/" + USERNAME + "/" + "Instruccion"; 
    instruccion.toCharArray(INSTRUCCION, 50);
}

void loop() {
    if(!client.connected()){
        reconnect();
    }
    client.loop();
}

void callback(char* topic, byte* payload, unsigned int length){
    char PAYLOAD[10] = "";
    String comando_separado = "";
    int indice = 0;
    int comandos[2];
    
    Serial.print("Mensaje Recibido: [");
    Serial.print(topic);
    Serial.print("] ");
    for (int i = 0; i < length; i++) {
        PAYLOAD[i] = (char)payload[i];
        if((char)payload[i] != ','){
            comando_separado = comando_separado + (char)payload[i];
        }else{
          comandos[indice] = comando_separado.toInt();
          comando_separado = "";
          indice++;
        }
    }
    if(comandos[0] == 0){
        if(comandos[1] == 1){
            digitalWrite(Foco, HIGH);
            Serial.println("Foco enciende");
        }
        if(comandos[1] == 0){
            digitalWrite(Foco, LOW);
            Serial.println("Foco apaga");
        }
    }
    // ACTIVAR DIMMER
    if(comandos[0] == 1){
        int nivel = comandos[1];
        int val = nivel*2;
        if (val>0){
          tarBrightness =val;
          Serial.println(tarBrightness);
        }
        Serial.println("Dimmer activado");
    }
}

void reconnect() {
    uint8_t retries = 3;
    while (!client.connected()) {
        Serial.print("Intentando conexion MQTT...");
        String clientId = "ESP8266Client-";
        clientId += String(random(0xffff), HEX);
        USERNAME.toCharArray(PLACA, 50);
        if(client.connect("", PLACA, PASSWORD)){
            Serial.println("conectado");
            client.subscribe(INSTRUCCION);
        }else{
            Serial.print("fallo, rc=");
            Serial.print(client.state());
            Serial.println(" intenta nuevamente en 5 segundos");
            delay(5000);
        }
        retries--;
        if(retries == 0) {
            while (1);
        }
    } 
}

void dimTimerISR() {
    if (fade == 1) {
      if (curBrightness > tarBrightness || (state == 0 && curBrightness >= 0)) {
        --curBrightness;
      }
      else if (curBrightness < tarBrightness && state == 1 && curBrightness <= 255) {
        ++curBrightness;
      }
    }
    else {
      if (state == 1) {
        curBrightness = tarBrightness;
      }
      else {
        curBrightness = 0;
      }
    }
    
    if (curBrightness == 0) {
      state = 0;
      digitalWrite(pwmPin, 0);
    }
    else if (curBrightness == 255) {
      state = 1;
      digitalWrite(pwmPin, 1);
    }
    else {
      digitalWrite(pwmPin, 1);
    }
    
    zcState = 0;
}

void zcDetectISR() {
  if (zcState == 0) {
    zcState = 1;
  
    if (curBrightness <= 255 && curBrightness >= 0) {
      digitalWrite(pwmPin, 0);
      
      int dimDelay = 30 * (255 - curBrightness) + 400;//400
      hw_timer_arm(dimDelay);
    }
  }
}
