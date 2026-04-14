import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/auth/presentation/view/widgets/custom_field_with_text_up_it_in_auth_view.dart';

class CustomTwoFieldsInAuthView extends StatelessWidget {
  const CustomTwoFieldsInAuthView({
    super.key,
    required this.nameTextEditingController,
    required this.passwordTextEditingController,
  });
  final TextEditingController nameTextEditingController,
      passwordTextEditingController;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        CustomFieldWithTextUpItInAuthView(
          text: 'اسم المستخدم',
          hintText: 'اسم المستخدم',
          textEditingController: nameTextEditingController,
        ),
        Heights.height19(context: context),
        CustomFieldWithTextUpItInAuthView(
          text: 'كلمة السر',
          hintText: 'كلمة السر',
          textEditingController: passwordTextEditingController,
        ),
      ],
    );
  }
}
