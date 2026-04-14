import 'package:flutter/material.dart';
import '/features/auth/presentation/view/widgets/custom_sliver_fill_remaining_in_auth_view.dart';

class CustomSuccessAuthStateInAuthView extends StatelessWidget {
  const CustomSuccessAuthStateInAuthView({
    super.key,
    required this.formKey,
    required this.autovalidateMode,
    required this.nameTextEditingController,
    required this.passwordTextEditingController,
    required this.isChecked,
    this.onChanged,
    required this.loginOnTap,
  });
  final GlobalKey<FormState> formKey;
  final AutovalidateMode autovalidateMode;
  final TextEditingController nameTextEditingController,
      passwordTextEditingController;
  final bool isChecked;
  final void Function(bool?)? onChanged;
  final void Function() loginOnTap;
  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        CustomSliverFillRemainingInAuthView(
          formKey: formKey,
          autovalidateMode: autovalidateMode,
          nameTextEditingController: nameTextEditingController,
          passwordTextEditingController: passwordTextEditingController,
          isChecked: isChecked,
          onChanged: onChanged,
          loginOnTap: loginOnTap,
        ),
      ],
    );
  }
}
