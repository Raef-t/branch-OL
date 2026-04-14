import 'package:flutter/material.dart';
import '/core/routers/app_router.dart';

class MaterialAppComponent extends StatelessWidget {
  const MaterialAppComponent({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      debugShowCheckedModeBanner: false,
      routerConfig: AppRouter.goRouter,
    );
  }
}
