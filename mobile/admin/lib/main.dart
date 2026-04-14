import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
// import 'package:flutter/services.dart';
import 'package:intl/date_symbol_data_local.dart';
import '/core/components/cupertino_app_component.dart';
import '/core/components/material_app_component.dart';
import '/core/service_locator/get_it_service_locator.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('ar');
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.black,
      // statusBarIconBrightness: Brightness.dark, // Android
      // statusBarBrightness: Brightness.light, // iOS
    ),
  );

  setUpServiceLocator();
  runApp(const SecondPageApp());
}

class SecondPageApp extends StatelessWidget {
  const SecondPageApp({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoAppComponent();
    } else {
      return const MaterialAppComponent();
    }
  }
}
