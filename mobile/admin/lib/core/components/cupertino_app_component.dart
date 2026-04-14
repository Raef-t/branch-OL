import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import '/core/routers/app_router.dart';

class CupertinoAppComponent extends StatelessWidget {
  const CupertinoAppComponent({super.key});

  @override
  Widget build(BuildContext context) {
    return CupertinoApp.router(
      debugShowCheckedModeBanner: false,
      routerConfig: AppRouter.goRouter,
      localizationsDelegates: const [
        DefaultMaterialLocalizations.delegate,
        DefaultCupertinoLocalizations.delegate,
        DefaultWidgetsLocalizations.delegate,
      ],
    );
  }
}
