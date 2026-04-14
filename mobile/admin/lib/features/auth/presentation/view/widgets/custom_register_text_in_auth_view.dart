import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';

class CustomRegisterTextInAuthView extends StatelessWidget {
  const CustomRegisterTextInAuthView({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(right: MediaQuery.sizeOf(context).width * 0.09),
      child: Align(
        alignment: Alignment.centerRight,
        child: Text('تسجيل الدخول', style: TextsStyle.bold24(context: context)),
      ),
    );
  }
}
